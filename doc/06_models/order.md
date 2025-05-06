# Order Model

`App\Models\Order`

Ce modèle représente les commandes passées par les utilisateurs, incluant les détails des articles, le statut de livraison et les informations de paiement.

## Structure de la Table

```php
Schema::create('orders', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('restaurant_id')->constrained();
    $table->foreignId('delivery_id')->nullable()->constrained();
    $table->string('status');
    $table->string('payment_status');
    $table->decimal('subtotal', 10, 2);
    $table->decimal('delivery_fee', 8, 2);
    $table->decimal('tax', 8, 2);
    $table->decimal('total', 10, 2);
    $table->json('delivery_address');
    $table->text('special_instructions')->nullable();
    $table->timestamp('estimated_delivery_time')->nullable();
    $table->json('payment_details')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Order extends Model
{
    // Appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Appartient à un restaurant
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // A une livraison
    public function delivery()
    {
        return $this->belongsTo(Delivery::class);
    }

    // A plusieurs articles
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // A un historique de statuts
    public function statusHistory()
    {
        return $this->hasMany(OrderStatus::class);
    }

    // A un avis
    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
```

## Attributs

```php
protected $fillable = [
    'user_id',
    'restaurant_id',
    'status',
    'payment_status',
    'subtotal',
    'delivery_fee',
    'tax',
    'total',
    'delivery_address',
    'special_instructions',
    'estimated_delivery_time'
];

protected $casts = [
    'delivery_address' => 'array',
    'payment_details' => 'array',
    'estimated_delivery_time' => 'datetime',
    'subtotal' => 'decimal:2',
    'total' => 'decimal:2'
];

// Statuts possibles de la commande
const STATUSES = [
    'PENDING' => 'pending',
    'CONFIRMED' => 'confirmed',
    'PREPARING' => 'preparing',
    'READY' => 'ready',
    'IN_DELIVERY' => 'in_delivery',
    'DELIVERED' => 'delivered',
    'CANCELLED' => 'cancelled'
];
```

## Scopes

```php
// Filtre par statut
public function scopeByStatus($query, $status)
{
    return $query->where('status', $status);
}

// Commandes récentes
public function scopeRecent($query)
{
    return $query->orderBy('created_at', 'desc');
}

// Commandes en cours
public function scopeInProgress($query)
{
    return $query->whereNotIn('status', [
        self::STATUSES['DELIVERED'],
        self::STATUSES['CANCELLED']
    ]);
}

// Commandes du jour
public function scopeToday($query)
{
    return $query->whereDate('created_at', today());
}
```

## Méthodes

```php
// Calcule le total de la commande
public function calculateTotal()
{
    $this->subtotal = $this->items->sum(function ($item) {
        return $item->price * $item->quantity;
    });
    
    $this->tax = $this->subtotal * config('order.tax_rate');
    $this->total = $this->subtotal + $this->tax + $this->delivery_fee;
    
    return $this->total;
}

// Met à jour le statut de la commande
public function updateStatus($status, $notes = null)
{
    if (!in_array($status, self::STATUSES)) {
        throw new InvalidStatusException("Invalid status: {$status}");
    }

    $this->status = $status;
    $this->save();

    $this->statusHistory()->create([
        'status' => $status,
        'notes' => $notes
    ]);

    event(new OrderStatusUpdated($this));
}

// Vérifie si la commande peut être annulée
public function canBeCancelled(): bool
{
    return in_array($this->status, [
        self::STATUSES['PENDING'],
        self::STATUSES['CONFIRMED']
    ]);
}

// Génère le numéro de suivi
public function generateTrackingNumber()
{
    return strtoupper(
        'ORD-' . $this->id . '-' . Str::random(6)
    );
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => OrderCreated::class,
    'updated' => OrderUpdated::class
];
```

## Observers

```php
class OrderObserver
{
    public function creating(Order $order)
    {
        $order->tracking_number = $order->generateTrackingNumber();
    }

    public function updated(Order $order)
    {
        if ($order->isDirty('status')) {
            Cache::tags(['orders'])->flush();
        }
    }
}
```

## Notifications

```php
// Notifications liées aux commandes
public function notifications()
{
    return [
        'status_update' => OrderStatusNotification::class,
        'delivery_update' => DeliveryUpdateNotification::class,
        'payment_confirmation' => PaymentConfirmationNotification::class
    ];
}
```

## Notes de Sécurité

- Validation des montants
- Vérification des statuts valides
- Protection des données de paiement
- Vérification des permissions
- Logging des changements de statut
- Validation des adresses de livraison 
