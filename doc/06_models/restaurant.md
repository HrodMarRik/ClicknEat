# Restaurant Model

`App\Models\Restaurant`

Ce modèle représente les restaurants dans l'application, incluant leurs informations, menus et horaires d'ouverture.

## Structure de la Table

```php
Schema::create('restaurants', function (Blueprint $table) {
    $table->id();
    $table->foreignId('owner_id')->constrained('users');
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description');
    $table->string('address');
    $table->decimal('latitude', 10, 8);
    $table->decimal('longitude', 10, 8);
    $table->string('phone');
    $table->string('email')->unique();
    $table->string('cuisine_type');
    $table->decimal('delivery_radius', 5, 2);
    $table->decimal('minimum_order', 8, 2)->default(0);
    $table->decimal('delivery_fee', 8, 2)->default(0);
    $table->boolean('is_active')->default(true);
    $table->boolean('is_featured')->default(false);
    $table->json('opening_hours');
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Restaurant extends Model
{
    // Appartient à un propriétaire (User)
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // A plusieurs menus
    public function menus()
    {
        return $this->hasMany(Menu::class);
    }

    // A plusieurs commandes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // A plusieurs avis
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // A plusieurs promotions
    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    // A plusieurs images
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
```

## Attributs

```php
protected $fillable = [
    'name',
    'description',
    'address',
    'latitude',
    'longitude',
    'phone',
    'email',
    'cuisine_type',
    'delivery_radius',
    'minimum_order',
    'delivery_fee',
    'opening_hours'
];

protected $casts = [
    'opening_hours' => 'array',
    'is_active' => 'boolean',
    'is_featured' => 'boolean',
    'latitude' => 'float',
    'longitude' => 'float'
];
```

## Accesseurs et Mutateurs

```php
// Génère le slug automatiquement
protected static function boot()
{
    parent::boot();
    static::creating(function ($restaurant) {
        $restaurant->slug = Str::slug($restaurant->name);
    });
}

// Récupère la note moyenne
public function getAverageRatingAttribute()
{
    return $this->reviews()->avg('rating') ?? 0;
}

// Vérifie si le restaurant est ouvert
public function getIsOpenAttribute()
{
    return $this->isCurrentlyOpen();
}

// Formate les horaires d'ouverture
public function setOpeningHoursAttribute($value)
{
    $this->attributes['opening_hours'] = is_array($value) 
        ? json_encode($value)
        : $value;
}
```

## Scopes

```php
// Filtre les restaurants actifs
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

// Filtre par type de cuisine
public function scopeByCuisine($query, $type)
{
    return $query->where('cuisine_type', $type);
}

// Filtre les restaurants à proximité
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
// Vérifie si le restaurant est actuellement ouvert
public function isCurrentlyOpen(): bool
{
    $now = now();
    $dayOfWeek = strtolower($now->format('l'));
    $currentTime = $now->format('H:i');
    
    $hours = $this->opening_hours[$dayOfWeek] ?? null;
    
    if (!$hours) {
        return false;
    }
    
    foreach ($hours as $period) {
        if ($currentTime >= $period['open'] && 
            $currentTime <= $period['close']) {
            return true;
        }
    }
    
    return false;
}

// Calcule le temps de livraison estimé
public function calculateDeliveryTime(): int
{
    $baseTime = 30; // temps de base en minutes
    $orderCount = $this->orders()
        ->where('status', 'preparing')
        ->count();
    
    return $baseTime + ($orderCount * 5);
}

// Vérifie si le restaurant livre à une adresse
public function deliversTo($latitude, $longitude): bool
{
    $distance = $this->getDistanceTo($latitude, $longitude);
    return $distance <= $this->delivery_radius;
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => RestaurantCreated::class,
    'updated' => RestaurantUpdated::class,
    'deleted' => RestaurantDeleted::class,
];
```

## Observers

```php
class RestaurantObserver
{
    public function creating(Restaurant $restaurant)
    {
        if (empty($restaurant->slug)) {
            $restaurant->slug = Str::slug($restaurant->name);
        }
    }

    public function updating(Restaurant $restaurant)
    {
        Cache::tags(['restaurants'])->flush();
    }
}
```

## Notes de Sécurité

- Validation des coordonnées géographiques
- Vérification des horaires d'ouverture
- Protection contre les injections SQL dans les requêtes géographiques
- Validation des emails uniques
- Vérification des permissions pour les modifications 
