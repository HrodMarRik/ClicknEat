# Notification Model

`App\Models\Notification`

Ce modèle représente les notifications envoyées aux utilisateurs via différents canaux (email, push, SMS, etc.).

## Structure de la Table

```php
Schema::create('notifications', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->string('type');
    $table->morphs('notifiable');
    $table->text('data');
    $table->timestamp('read_at')->nullable();
    $table->timestamp('sent_at')->nullable();
    $table->string('channel')->nullable();
    $table->json('channels_status')->nullable();
    $table->integer('retry_count')->default(0);
    $table->timestamp('last_retry_at')->nullable();
    $table->text('error_message')->nullable();
    $table->timestamps();
});
```

## Relations

```php
class Notification extends Model
{
    // Relation polymorphique avec l'entité notifiable
    public function notifiable()
    {
        return $this->morphTo();
    }

    // A plusieurs tentatives d'envoi
    public function attempts()
    {
        return $this->hasMany(NotificationAttempt::class);
    }
}
```

## Attributs

```php
protected $fillable = [
    'type',
    'data',
    'read_at',
    'sent_at',
    'channel',
    'channels_status',
    'retry_count',
    'last_retry_at',
    'error_message'
];

protected $casts = [
    'data' => 'array',
    'channels_status' => 'array',
    'read_at' => 'datetime',
    'sent_at' => 'datetime',
    'last_retry_at' => 'datetime'
];

// Types de notification
const TYPES = [
    'ORDER_STATUS' => 'App\Notifications\OrderStatusNotification',
    'DELIVERY_UPDATE' => 'App\Notifications\DeliveryUpdateNotification',
    'PAYMENT_CONFIRMATION' => 'App\Notifications\PaymentConfirmationNotification',
    'PROMOTION' => 'App\Notifications\PromotionNotification'
];

// Canaux de notification
const CHANNELS = [
    'MAIL' => 'mail',
    'DATABASE' => 'database',
    'PUSH' => 'push',
    'SMS' => 'sms'
];
```

## Scopes

```php
// Notifications non lues
public function scopeUnread($query)
{
    return $query->whereNull('read_at');
}

// Notifications par type
public function scopeOfType($query, $type)
{
    return $query->where('type', $type);
}

// Notifications par canal
public function scopeByChannel($query, $channel)
{
    return $query->where('channel', $channel);
}

// Notifications échouées
public function scopeFailed($query)
{
    return $query->whereNotNull('error_message');
}

// Notifications à réessayer
public function scopeToRetry($query, $maxRetries = 3)
{
    return $query->whereNull('sent_at')
        ->where('retry_count', '<', $maxRetries)
        ->where(function ($q) {
            $q->whereNull('last_retry_at')
              ->orWhere('last_retry_at', '<=', now()->subMinutes(15));
        });
}
```

## Méthodes

```php
// Marque la notification comme lue
public function markAsRead()
{
    $this->update(['read_at' => now()]);
}

// Marque la notification comme envoyée
public function markAsSent()
{
    $this->update([
        'sent_at' => now(),
        'error_message' => null
    ]);
}

// Enregistre une erreur d'envoi
public function logError($error)
{
    $this->update([
        'error_message' => $error,
        'retry_count' => $this->retry_count + 1,
        'last_retry_at' => now()
    ]);

    $this->attempts()->create([
        'status' => 'failed',
        'error_message' => $error
    ]);
}

// Envoie la notification
public function send()
{
    try {
        $notificationClass = $this->type;
        $notification = new $notificationClass($this->data);
        
        $channels = $this->getChannels();
        
        foreach ($channels as $channel) {
            $status = $this->sendToChannel($notification, $channel);
            $this->updateChannelStatus($channel, $status);
        }

        if ($this->allChannelsSucceeded()) {
            $this->markAsSent();
            return true;
        }

        return false;
    } catch (\Exception $e) {
        $this->logError($e->getMessage());
        return false;
    }
}

// Obtient les canaux de notification
protected function getChannels(): array
{
    $notifiable = $this->notifiable;
    $notification = new $this->type($this->data);

    return array_intersect(
        $notification->via($notifiable),
        $notifiable->preferredChannels()
    );
}

// Envoie à un canal spécifique
protected function sendToChannel($notification, $channel)
{
    $sender = app(NotificationSender::class);
    return $sender->send(
        $this->notifiable,
        $notification,
        $channel
    );
}

// Met à jour le statut d'un canal
protected function updateChannelStatus($channel, $status)
{
    $currentStatus = $this->channels_status ?? [];
    $currentStatus[$channel] = $status;
    
    $this->update(['channels_status' => $currentStatus]);
}

// Vérifie si tous les canaux ont réussi
protected function allChannelsSucceeded(): bool
{
    return collect($this->channels_status)
        ->every(function ($status) {
            return $status === 'success';
        });
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => NotificationCreated::class,
    'updated' => NotificationUpdated::class
];
```

## Observers

```php
class NotificationObserver
{
    public function created(Notification $notification)
    {
        if (config('notifications.send_immediately')) {
            SendNotification::dispatch($notification);
        }
    }
}
```

## Notes de Sécurité

- Validation des types de notification
- Vérification des permissions
- Protection contre le spam
- Rate limiting
- Validation des canaux
- Sécurisation des données sensibles
- Gestion des erreurs
- Logging des tentatives 