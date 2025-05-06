# Menu Model

`App\Models\Menu`

Ce modèle représente les menus des restaurants, incluant les catégories, les plats et leurs options.

## Structure de la Table

```php
Schema::create('menus', function (Blueprint $table) {
    $table->id();
    $table->foreignId('restaurant_id')->constrained();
    $table->string('name');
    $table->text('description')->nullable();
    $table->boolean('is_active')->default(true);
    $table->json('availability')->nullable();
    $table->timestamp('available_from')->nullable();
    $table->timestamp('available_until')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Menu extends Model
{
    // Appartient à un restaurant
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // A plusieurs catégories
    public function categories()
    {
        return $this->hasMany(Category::class)->orderBy('position');
    }

    // A plusieurs plats à travers les catégories
    public function items()
    {
        return $this->hasManyThrough(MenuItem::class, Category::class);
    }

    // A plusieurs promotions actives
    public function activePromotions()
    {
        return $this->hasMany(Promotion::class)
            ->where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }
}
```

## Attributs

```php
protected $fillable = [
    'restaurant_id',
    'name',
    'description',
    'is_active',
    'availability',
    'available_from',
    'available_until'
];

protected $casts = [
    'is_active' => 'boolean',
    'availability' => 'array',
    'available_from' => 'datetime',
    'available_until' => 'datetime'
];
```

## Scopes

```php
// Menus actifs
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

// Menus disponibles actuellement
public function scopeAvailableNow($query)
{
    return $query->where('is_active', true)
        ->where(function ($q) {
            $q->whereNull('available_from')
                ->orWhere('available_from', '<=', now());
        })
        ->where(function ($q) {
            $q->whereNull('available_until')
                ->orWhere('available_until', '>=', now());
        });
}

// Menus avec des promotions actives
public function scopeWithActivePromotions($query)
{
    return $query->whereHas('activePromotions');
}
```

## Méthodes

```php
// Vérifie si le menu est disponible
public function isAvailable(): bool
{
    if (!$this->is_active) {
        return false;
    }

    $now = now();
    $dayOfWeek = strtolower($now->format('l'));
    
    // Vérifie la disponibilité journalière
    if (isset($this->availability[$dayOfWeek])) {
        foreach ($this->availability[$dayOfWeek] as $period) {
            if ($now->between(
                Carbon::parse($period['start']),
                Carbon::parse($period['end'])
            )) {
                return true;
            }
        }
        return false;
    }

    // Vérifie la période de disponibilité globale
    return (!$this->available_from || $now->gte($this->available_from)) &&
           (!$this->available_until || $now->lte($this->available_until));
}

// Ajoute une catégorie
public function addCategory(array $data): Category
{
    $position = $this->categories()->max('position') + 1;
    
    return $this->categories()->create(array_merge(
        $data,
        ['position' => $position]
    ));
}

// Réorganise les catégories
public function reorderCategories(array $categoryIds): bool
{
    DB::transaction(function () use ($categoryIds) {
        foreach ($categoryIds as $position => $id) {
            $this->categories()
                ->where('id', $id)
                ->update(['position' => $position]);
        }
    });
    
    return true;
}

// Récupère tous les plats disponibles
public function getAvailableItems()
{
    return $this->items()
        ->where('is_available', true)
        ->whereHas('category', function ($query) {
            $query->where('is_active', true);
        })
        ->with(['options', 'allergens'])
        ->get();
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => MenuCreated::class,
    'updated' => MenuUpdated::class,
    'deleted' => MenuDeleted::class
];
```

## Observers

```php
class MenuObserver
{
    public function saved(Menu $menu)
    {
        Cache::tags(['restaurant_menus'])->forget(
            "menu:{$menu->restaurant_id}"
        );
    }

    public function deleted(Menu $menu)
    {
        $menu->categories()->delete();
        Cache::tags(['restaurant_menus'])->flush();
    }
}
```

## Cache

```php
public function getCachedCategories()
{
    return Cache::tags(['menu_categories'])
        ->remember("menu:{$this->id}:categories", 3600, function () {
            return $this->categories()
                ->with(['items' => function ($query) {
                    $query->where('is_available', true);
                }])
                ->get();
        });
}
```

## Notes de Sécurité

- Validation des périodes de disponibilité
- Vérification des permissions
- Protection contre les modifications non autorisées
- Validation des relations
- Nettoyage des données JSON
- Gestion sécurisée du cache 
