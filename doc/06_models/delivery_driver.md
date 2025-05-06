# DeliveryDriver Model

`App\Models\DeliveryDriver`

Ce modèle représente les livreurs et leurs informations spécifiques à la livraison.

## Structure de la Table

```php
Schema::create('delivery_drivers', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('vehicle_type');
    $table->string('vehicle_model')->nullable();
    $table->string('vehicle_plate')->nullable();
    $table->string('license_number');
    $table->date('license_expiry');
    $table->string('insurance_number');
    $table->date('insurance_expiry');
    $table->boolean('is_active')->default(true);
    $table->boolean('is_available')->default(false);
    $table->json('working_hours')->nullable();
    $table->json('delivery_zones')->nullable();
    $table->decimal('current_latitude', 10, 8)->nullable();
    $table->decimal('current_longitude', 10, 8)->nullable();
    $table->timestamp('location_updated_at')->nullable();
    $table->json('ratings')->nullable();
    $table->decimal('average_rating', 3, 2)->default(0);
    $table->integer('completed_deliveries')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class DeliveryDriver extends Model
{
    // Appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A plusieurs livraisons
    public function deliveries()
    {
        return $this->hasMany(Delivery::class, 'driver_id', 'user_id');
    }

    // A plusieurs livraisons actives
    public function activeDeliveries()
    {
        return $this->deliveries()
            ->whereNotIn('status', [
                Delivery::STATUSES['DELIVERED'],
                Delivery::STATUSES['CANCELLED']
            ]);
    }

    // A plusieurs documents
    public function documents()
    {
        return $this->hasMany(DriverDocument::class, 'driver_id', 'user_id');
    }

    // A plusieurs évaluations
    public function ratings()
    {
        return $this->hasMany(DriverRating::class, 'driver_id', 'user_id');
    }
}
```

## Attributs

```php
protected $fillable = [
    'user_id',
    'vehicle_type',
    'vehicle_model',
    'vehicle_plate',
    'license_number',
    'license_expiry',
    'insurance_number',
    'insurance_expiry',
    'is_active',
    'is_available',
    'working_hours',
    'delivery_zones',
    'current_latitude',
    'current_longitude',
    'ratings'
];

protected $casts = [
    'is_active' => 'boolean',
    'is_available' => 'boolean',
    'working_hours' => 'array',
    'delivery_zones' => 'array',
    'ratings' => 'array',
    'license_expiry' => 'date',
    'insurance_expiry' => 'date',
    'location_updated_at' => 'datetime',
    'current_latitude' => 'decimal:8',
    'current_longitude' => 'decimal:8',
    'average_rating' => 'decimal:2'
];

// Types de véhicules
const VEHICLE_TYPES = [
    'BICYCLE' => 'bicycle',
    'SCOOTER' => 'scooter',
    'MOTORCYCLE' => 'motorcycle',
    'CAR' => 'car'
];
```

## Scopes

```php
// Livreurs actifs
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

// Livreurs disponibles
public function scopeAvailable($query)
{
    return $query->where('is_available', true);
}

// Livreurs par type de véhicule
public function scopeByVehicleType($query, $type)
{
    return $query->where('vehicle_type', $type);
}

// Livreurs à proximité
public function scopeNearby($query, $latitude, $longitude, $radius = 5)
{
    return $query->whereRaw(
        'ST_Distance_Sphere(
            point(current_longitude, current_latitude),
            point(?, ?)
        ) <= ? * 1000',
        [$longitude, $latitude, $radius]
    );
}

// Livreurs avec documents valides
public function scopeWithValidDocuments($query)
{
    return $query->where('license_expiry', '>', now())
        ->where('insurance_expiry', '>', now());
}
```

## Méthodes

```php
// Met à jour la position
public function updateLocation($latitude, $longitude)
{
    $this->update([
        'current_latitude' => $latitude,
        'current_longitude' => $longitude,
        'location_updated_at' => now()
    ]);

    event(new DriverLocationUpdated($this));
}

// Vérifie la disponibilité
public function isAvailableForDelivery(): bool
{
    if (!$this->is_active || !$this->is_available) {
        return false;
    }

    if ($this->activeDeliveries()->count() >= config('delivery.max_concurrent_deliveries')) {
        return false;
    }

    return $this->isWorkingNow() && $this->hasValidDocuments();
}

// Vérifie si le livreur travaille actuellement
public function isWorkingNow(): bool
{
    $now = now();
    $dayOfWeek = strtolower($now->format('l'));
    
    if (!isset($this->working_hours[$dayOfWeek])) {
        return false;
    }

    foreach ($this->working_hours[$dayOfWeek] as $period) {
        if ($now->between(
            Carbon::parse($period['start']),
            Carbon::parse($period['end'])
        )) {
            return true;
        }
    }

    return false;
}

// Vérifie la validité des documents
public function hasValidDocuments(): bool
{
    return $this->license_expiry->isFuture() &&
           $this->insurance_expiry->isFuture();
}

// Calcule la distance jusqu'à un point
public function distanceTo($latitude, $longitude): float
{
    return app(GeocodeService::class)->calculateDistance(
        $this->current_latitude,
        $this->current_longitude,
        $latitude,
        $longitude
    );
}

// Ajoute une évaluation
public function addRating(int $rating, ?string $comment = null)
{
    $this->ratings()->create([
        'rating' => $rating,
        'comment' => $comment
    ]);

    $this->updateAverageRating();
}

// Met à jour la note moyenne
public function updateAverageRating()
{
    $this->average_rating = $this->ratings()
        ->avg('rating') ?? 0;
    $this->save();
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => DeliveryDriverCreated::class,
    'updated' => DeliveryDriverUpdated::class
];
```

## Observers

```php
class DeliveryDriverObserver
{
    public function creating(DeliveryDriver $driver)
    {
        if ($driver->delivery_zones === null) {
            $driver->delivery_zones = config('delivery.default_zones');
        }
    }

    public function updating(DeliveryDriver $driver)
    {
        if ($driver->isDirty('current_latitude', 'current_longitude')) {
            Cache::tags(['driver_locations'])->forget(
                "driver:{$driver->id}:location"
            );
        }
    }
}
```

## Validation

```php
class DeliveryDriverValidator
{
    public static function rules()
    {
        return [
            'vehicle_type' => ['required', Rule::in(DeliveryDriver::VEHICLE_TYPES)],
            'vehicle_plate' => 'required|string|max:20',
            'license_number' => 'required|string|max:50',
            'license_expiry' => 'required|date|after:today',
            'insurance_number' => 'required|string|max:50',
            'insurance_expiry' => 'required|date|after:today',
            'working_hours' => 'required|array',
            'delivery_zones' => 'nullable|array'
        ];
    }
}
```

## Notes de Sécurité

- Validation des coordonnées GPS
- Vérification des documents
- Protection contre le spoofing de localisation
- Vérification des permissions
- Validation des zones de livraison
- Protection des données personnelles
- Rate limiting sur les mises à jour de position
- Validation des horaires de travail 
