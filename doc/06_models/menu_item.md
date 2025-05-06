# MenuItem Model

`App\Models\MenuItem`

Ce modèle représente les plats individuels dans le menu d'un restaurant, incluant leurs options, allergènes et informations nutritionnelles.

## Structure de la Table

```php
Schema::create('menu_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('category_id')->constrained();
    $table->string('name');
    $table->text('description')->nullable();
    $table->decimal('price', 8, 2);
    $table->boolean('is_available')->default(true);
    $table->boolean('is_featured')->default(false);
    $table->integer('preparation_time')->nullable();
    $table->string('image')->nullable();
    $table->json('allergens')->nullable();
    $table->json('nutritional_info')->nullable();
    $table->integer('position')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class MenuItem extends Model
{
    // Appartient à une catégorie
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // A plusieurs options
    public function options()
    {
        return $this->hasMany(MenuItemOption::class);
    }

    // A plusieurs variantes
    public function variants()
    {
        return $this->hasMany(MenuItemVariant::class);
    }

    // Apparaît dans plusieurs commandes
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // A plusieurs images
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    // A plusieurs promotions
    public function promotions()
    {
        return $this->belongsToMany(Promotion::class);
    }
}
```

## Attributs

```php
protected $fillable = [
    'category_id',
    'name',
    'description',
    'price',
    'is_available',
    'is_featured',
    'preparation_time',
    'image',
    'allergens',
    'nutritional_info',
    'position'
];

protected $casts = [
    'price' => 'decimal:2',
    'is_available' => 'boolean',
    'is_featured' => 'boolean',
    'allergens' => 'array',
    'nutritional_info' => 'array'
];

// Liste des allergènes possibles
const ALLERGENS = [
    'gluten',
    'crustaceans',
    'eggs',
    'fish',
    'peanuts',
    'soybeans',
    'milk',
    'nuts',
    'celery',
    'mustard',
    'sesame',
    'sulphites',
    'lupin',
    'molluscs'
];
```

## Scopes

```php
// Plats disponibles
public function scopeAvailable($query)
{
    return $query->where('is_available', true);
}

// Plats en vedette
public function scopeFeatured($query)
{
    return $query->where('is_featured', true);
}

// Plats par gamme de prix
public function scopeByPriceRange($query, $min, $max)
{
    return $query->whereBetween('price', [$min, $max]);
}

// Plats sans certains allergènes
public function scopeWithoutAllergens($query, array $allergens)
{
    return $query->where(function ($q) use ($allergens) {
        $q->whereNull('allergens')
          ->orWhereRaw('NOT JSON_CONTAINS(allergens, ?)', [json_encode($allergens)]);
    });
}
```

## Méthodes

```php
// Calcule le prix avec les options
public function calculatePriceWithOptions(array $selectedOptions): float
{
    $basePrice = $this->price;
    
    $optionsPrice = collect($selectedOptions)
        ->map(function ($optionId) {
            return $this->options->find($optionId)->price ?? 0;
        })
        ->sum();
    
    return $basePrice + $optionsPrice;
}

// Vérifie la disponibilité des options
public function validateOptions(array $selectedOptions): bool
{
    return $this->options
        ->whereIn('id', $selectedOptions)
        ->where('is_available', true)
        ->count() === count($selectedOptions);
}

// Met à jour l'image du plat
public function updateImage($image)
{
    if ($this->image) {
        Storage::delete($this->image);
    }

    $path = $image->store('menu-items', 'public');
    $this->update(['image' => $path]);
}

// Génère une description SEO
public function generateSeoDescription(): string
{
    $description = "{$this->name} - {$this->price}€";
    
    if ($this->allergens) {
        $description .= ". Contient: " . implode(', ', $this->allergens);
    }
    
    return Str::limit($description, 160);
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => MenuItemCreated::class,
    'updated' => MenuItemUpdated::class,
    'deleted' => MenuItemDeleted::class
];
```

## Observers

```php
class MenuItemObserver
{
    public function creating(MenuItem $item)
    {
        if (!$item->position) {
            $item->position = $item->category
                ->items()
                ->max('position') + 1;
        }
    }

    public function deleted(MenuItem $item)
    {
        Storage::delete($item->image);
        Cache::tags(['menu_items'])->flush();
    }
}
```

## Cache

```php
public function getCachedOptions()
{
    return Cache::tags(['menu_item_options'])
        ->remember("item:{$this->id}:options", 3600, function () {
            return $this->options()
                ->where('is_available', true)
                ->get();
        });
}
```

## Notes de Sécurité

- Validation des prix
- Vérification des allergènes valides
- Protection des images uploadées
- Validation des options
- Nettoyage des données JSON
- Vérification des permissions
- Gestion sécurisée du cache 
