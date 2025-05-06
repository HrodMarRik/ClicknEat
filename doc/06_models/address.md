# Address Model

`App\Models\Address`

Ce modèle représente les adresses enregistrées par les utilisateurs pour la livraison et la facturation.

## Structure de la Table

```php
Schema::create('addresses', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('type');
    $table->string('name');
    $table->string('street_address');
    $table->string('apartment')->nullable();
    $table->string('city');
    $table->string('state');
    $table->string('postal_code');
    $table->string('country', 2);
    $table->decimal('latitude', 10, 8)->nullable();
    $table->decimal('longitude', 10, 8)->nullable();
    $table->text('delivery_instructions')->nullable();
    $table->boolean('is_default')->default(false);
    $table->boolean('is_verified')->default(false);
    $table->json('meta_data')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Address extends Model
{
    // Appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Utilisée dans plusieurs commandes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Utilisée dans plusieurs factures
    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }
}
```

## Attributs

```php
protected $fillable = [
    'user_id',
    'type',
    'name',
    'street_address',
    'apartment',
    'city',
    'state',
    'postal_code',
    'country',
    'latitude',
    'longitude',
    'delivery_instructions',
    'is_default',
    'meta_data'
];

protected $casts = [
    'latitude' => 'decimal:8',
    'longitude' => 'decimal:8',
    'is_default' => 'boolean',
    'is_verified' => 'boolean',
    'meta_data' => 'array'
];

// Types d'adresse
const TYPES = [
    'DELIVERY' => 'delivery',
    'BILLING' => 'billing',
    'BOTH' => 'both'
];
```

## Scopes

```php
// Adresses de livraison
public function scopeDelivery($query)
{
    return $query->whereIn('type', [self::TYPES['DELIVERY'], self::TYPES['BOTH']]);
}

// Adresses de facturation
public function scopeBilling($query)
{
    return $query->whereIn('type', [self::TYPES['BILLING'], self::TYPES['BOTH']]);
}

// Adresses vérifiées
public function scopeVerified($query)
{
    return $query->where('is_verified', true);
}

// Adresses dans un rayon donné
public function scopeNearby($query, $latitude, $longitude, $radius = 5)
{
    return $query->whereRaw(
        'ST_Distance_Sphere(
            point(longitude, latitude),
            point(?, ?)
        ) <= ? * 1000',
        [$longitude, $latitude, $radius]
    );
}
```

## Méthodes

```php
// Formate l'adresse complète
public function getFullAddressAttribute(): string
{
    $parts = [
        $this->street_address,
        $this->apartment,
        $this->city,
        $this->state,
        $this->postal_code,
        Countries::getName($this->country)
    ];

    return implode(', ', array_filter($parts));
}

// Définit comme adresse par défaut
public function setAsDefault()
{
    DB::transaction(function () {
        $this->user->addresses()
            ->where('type', $this->type)
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
            
        $this->update(['is_default' => true]);
    });
}

// Géocode l'adresse
public function geocode()
{
    try {
        $result = app(GeocodeService::class)->geocode($this->getFullAddressAttribute());
        
        $this->update([
            'latitude' => $result['latitude'],
            'longitude' => $result['longitude'],
            'is_verified' => true,
            'meta_data' => array_merge(
                $this->meta_data ?? [],
                ['geocode_data' => $result]
            )
        ]);

        return true;
    } catch (\Exception $e) {
        report($e);
        return false;
    }
}

// Calcule la distance avec un point
public function distanceTo($latitude, $longitude): float
{
    return app(GeocodeService::class)->calculateDistance(
        $this->latitude,
        $this->longitude,
        $latitude,
        $longitude
    );
}

// Vérifie si l'adresse est dans une zone de livraison
public function isInDeliveryZone(Restaurant $restaurant): bool
{
    return $this->distanceTo(
        $restaurant->latitude,
        $restaurant->longitude
    ) <= $restaurant->delivery_radius;
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => AddressCreated::class,
    'updated' => AddressUpdated::class
];
```

## Observers

```php
class AddressObserver
{
    public function creating(Address $address)
    {
        if ($address->user->addresses()->count() === 0) {
            $address->is_default = true;
        }
    }

    public function created(Address $address)
    {
        if (!$address->latitude || !$address->longitude) {
            $address->geocode();
        }
    }

    public function deleted(Address $address)
    {
        if ($address->is_default) {
            $newDefault = $address->user
                ->addresses()
                ->where('type', $address->type)
                ->where('id', '!=', $address->id)
                ->first();
                
            if ($newDefault) {
                $newDefault->setAsDefault();
            }
        }
    }
}
```

## Validation

```php
class AddressValidator
{
    public static function rules()
    {
        return [
            'type' => ['required', Rule::in(Address::TYPES)],
            'name' => 'required|string|max:255',
            'street_address' => 'required|string|max:255',
            'apartment' => 'nullable|string|max:50',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|size:2|exists:countries,code',
            'delivery_instructions' => 'nullable|string|max:500'
        ];
    }
}
```

## Notes de Sécurité

- Validation des coordonnées GPS
- Vérification des adresses
- Protection contre le geocoding abusif
- Vérification des permissions
- Validation des codes postaux
- Protection des données personnelles
- Rate limiting sur le geocoding
- Validation des pays autorisés 
