# Category Model

`App\Models\Category`

Ce modèle représente les catégories de plats dans les menus des restaurants.

## Structure de la Table

```php
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('menu_id')->constrained();
    $table->string('name');
    $table->string('slug')->unique();
    $table->text('description')->nullable();
    $table->string('image')->nullable();
    $table->boolean('is_active')->default(true);
    $table->integer('position')->default(0);
    $table->json('availability')->nullable();
    $table->timestamp('available_from')->nullable();
    $table->timestamp('available_until')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Category extends Model
{
    // Appartient à un menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }

    // A plusieurs plats
    public function items()
    {
        return $this->hasMany(MenuItem::class)->orderBy('position');
    }

    // A plusieurs promotions
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class);
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
    'menu_id',
    'name',
    'description',
    'image',
    'is_active',
    'position',
    'availability',
    'available_from',
    'available_until'
];

protected $casts = [
    'is_active' => 'boolean',
    'position' => 'integer',
    'availability' => 'array',
    'available_from' => 'datetime',
    'available_until' => 'datetime'
];
```

## Scopes

```php
// Catégories actives
public function scopeActive($query)
{
    return $query->where('is_active', true);
}

// Catégories disponibles actuellement
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

// Catégories avec des plats disponibles
public function scopeWithAvailableItems($query)
{
    return $query->whereHas('items', function ($q) {
        $q->where('is_available', true);
    });
}

// Catégories ordonnées par position
public function scopeOrdered($query)
{
    return $query->orderBy('position');
}
```

## Méthodes

```php
// Vérifie si la catégorie est disponible
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

// Ajoute un plat à la catégorie
public function addItem(array $data): MenuItem
{
    $position = $this->items()->max('position') + 1;
    
    return $this->items()->create(array_merge(
        $data,
        ['position' => $position]
    ));
}

// Réorganise les plats
public function reorderItems(array $itemIds): bool
{
    DB::transaction(function () use ($itemIds) {
        foreach ($itemIds as $position => $id) {
            $this->items()
                ->where('id', $id)
                ->update(['position' => $position]);
        }
    });
    
    return true;
}

// Met à jour l'image de la catégorie
public function updateImage($image)
{
    if ($this->image) {
        Storage::delete($this->image);
    }

    $path = $image->store('categories', 'public');
    $this->update(['image' => $path]);
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => CategoryCreated::class,
    'updated' => CategoryUpdated::class,
    'deleted' => CategoryDeleted::class
];
```

## Observers

```php
class CategoryObserver
{
    public function creating(Category $category)
    {
        if (empty($category->slug)) {
            $category->slug = Str::slug($category->name);
        }

        if (!$category->position) {
            $category->position = $category->menu
                ->categories()
                ->max('position') + 1;
        }
    }

    public function deleted(Category $category)
    {
        Storage::delete($category->image);
        Cache::tags(['menu_categories'])->flush();
    }
}
```

## Cache

```php
public function getCachedItems()
{
    return Cache::tags(['category_items'])
        ->remember("category:{$this->id}:items", 3600, function () {
            return $this->items()
                ->where('is_available', true)
                ->ordered()
                ->get();
        });
}
```

## Notes de Sécurité

- Validation des périodes de disponibilité
- Protection des images uploadées
- Vérification des permissions
- Validation des relations
- Nettoyage des données JSON
- Gestion sécurisée du cache
- Protection contre les modifications non autorisées 
