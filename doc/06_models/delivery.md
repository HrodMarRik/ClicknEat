# Delivery Model

`App\Models\Delivery`

Ce modèle représente les livraisons des commandes, incluant le suivi en temps réel, les statuts et les informations du livreur.

## Structure de la Table

```php
Schema::create('deliveries', function (Blueprint $table) {
    $table->id();
    $table->foreignId('order_id')->constrained();
    $table->foreignId('driver_id')->nullable()->constrained('users');
    $table->string('status');
    $table->json('pickup_location');
    $table->json('delivery_location');
    $table->timestamp('picked_up_at')->nullable();
    $table->timestamp('delivered_at')->nullable();
    $table->decimal('distance', 8, 2)->nullable();
    $table->integer('estimated_duration')->nullable();
    $table->text('delivery_notes')->nullable();
    $table->json('route_history')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Delivery extends Model
{
    // Appartient à une commande
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Appartient à un livreur
    public function driver()
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    // A plusieurs mises à jour de statut
    public function statusUpdates()
    {
        return $this->hasMany(DeliveryStatus::class);
    }

    // A plusieurs points de localisation
    public function locations()
    {
        return $this->hasMany(DeliveryLocation::class);
    }
}
```

## Attributs

```php
protected $fillable = [
    'order_id',
    'driver_id',
    'status',
    'pickup_location',
    'delivery_location',
    'picked_up_at',
    'delivered_at',
    'distance',
    'estimated_duration',
    'delivery_notes',
    'route_history'
];

protected $casts = [
    'pickup_location' => 'array',
    'delivery_location' => 'array',
    'picked_up_at' => 'datetime',
    'delivered_at' => 'datetime',
    'route_history' => 'array',
    'distance' => 'decimal:2'
];

// Statuts possibles de la livraison
const STATUSES = [
    'PENDING' => 'pending',
    'ASSIGNED' => 'assigned',
    'PICKED_UP' => 'picked_up',
    'IN_TRANSIT' => 'in_transit',
    'DELIVERED' => 'delivered',
    'FAILED' => 'failed'
];
```

## Scopes

```php
// Livraisons en cours
public function scopeInProgress($query)
{
    return $query->whereNotIn('status', [
        self::STATUSES['DELIVERED'],
        self::STATUSES['FAILED']
    ]);
}

// Livraisons du jour
public function scopeToday($query)
{
    return $query->whereDate('created_at', today());
}

// Livraisons par livreur
public function scopeByDriver($query, $driverId)
{
    return $query->where('driver_id', $driverId);
}

// Livraisons à proximité
public function scopeNearby($query, $latitude, $longitude, $radius = 5)
{
    return $query->whereRaw(
        'ST_Distance_Sphere(
            point(delivery_location->>"$.longitude", delivery_location->>"$.latitude"),
            point(?, ?)
        ) <= ? * 1000',
        [$longitude, $latitude, $radius]
    );
}
```

## Méthodes

```php
// Met à jour le statut de la livraison
public function updateStatus($status, $location = null)
{
    if (!in_array($status, self::STATUSES)) {
        throw new InvalidStatusException("Invalid status: {$status}");
    }

    DB::transaction(function () use ($status, $location) {
        $this->status = $status;
        
        if ($status === self::STATUSES['PICKED_UP']) {
            $this->picked_up_at = now();
        } elseif ($status === self::STATUSES['DELIVERED']) {
            $this->delivered_at = now();
        }

        if ($location) {
            $this->addLocationToHistory($location);
        }

        $this->save();

        $this->statusUpdates()->create([
            'status' => $status,
            'location' => $location
        ]);

        event(new DeliveryStatusUpdated($this));
    });
}

// Ajoute une position à l'historique
public function addLocationToHistory($location)
{
    $history = $this->route_history ?? [];
    $history[] = array_merge(
        $location,
        ['timestamp' => now()->toDateTimeString()]
    );
    
    $this->route_history = $history;
}

// Calcule la distance totale parcourue
public function calculateTotalDistance(): float
{
    if (empty($this->route_history)) {
        return 0;
    }

    $distance = 0;
    $points = collect($this->route_history);

    $points->each(function ($point, $index) use (&$distance, $points) {
        if ($index === 0) return;

        $previousPoint = $points[$index - 1];
        $distance += $this->calculateDistanceBetweenPoints(
            $previousPoint['latitude'],
            $previousPoint['longitude'],
            $point['latitude'],
            $point['longitude']
        );
    });

    return round($distance, 2);
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => DeliveryCreated::class,
    'updated' => DeliveryUpdated::class
];
```

## Observers

```php
class DeliveryObserver
{
    public function created(Delivery $delivery)
    {
        $delivery->updateEstimatedDuration();
    }

    public function updated(Delivery $delivery)
    {
        if ($delivery->isDirty('status')) {
            Cache::tags(['deliveries'])->flush();
        }
    }
}
```

## Notifications

```php
// Notifications liées aux livraisons
public function notifications()
{
    return [
        'status_update' => DeliveryStatusNotification::class,
        'driver_assigned' => DriverAssignedNotification::class,
        'delivery_completed' => DeliveryCompletedNotification::class
    ];
}
```

## Notes de Sécurité

- Validation des coordonnées GPS
- Protection contre le spoofing de localisation
- Vérification des permissions
- Validation des statuts
- Logging des changements de statut
- Sécurisation des données de localisation
- Rate limiting sur les mises à jour de position 
