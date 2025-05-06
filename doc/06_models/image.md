# Image Model

`App\Models\Image`

Ce modèle représente les images associées aux différentes entités de l'application (restaurants, plats, utilisateurs, etc.).

## Structure de la Table

```php
Schema::create('images', function (Blueprint $table) {
    $table->id();
    $table->morphs('imageable');
    $table->string('path');
    $table->string('filename');
    $table->string('mime_type');
    $table->integer('size');
    $table->string('disk')->default('public');
    $table->string('collection')->nullable();
    $table->json('custom_properties')->nullable();
    $table->integer('width')->nullable();
    $table->integer('height')->nullable();
    $table->boolean('is_optimized')->default(false);
    $table->integer('order')->default(0);
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Image extends Model
{
    // Relation polymorphique
    public function imageable()
    {
        return $this->morphTo();
    }

    // A plusieurs conversions
    public function conversions()
    {
        return $this->hasMany(ImageConversion::class);
    }
}
```

## Attributs

```php
protected $fillable = [
    'path',
    'filename',
    'mime_type',
    'size',
    'disk',
    'collection',
    'custom_properties',
    'width',
    'height',
    'is_optimized',
    'order'
];

protected $casts = [
    'custom_properties' => 'array',
    'is_optimized' => 'boolean',
    'width' => 'integer',
    'height' => 'integer',
    'size' => 'integer',
    'order' => 'integer'
];

// Collections d'images
const COLLECTIONS = [
    'AVATARS' => 'avatars',
    'RESTAURANTS' => 'restaurants',
    'MENU_ITEMS' => 'menu-items',
    'CATEGORIES' => 'categories',
    'BANNERS' => 'banners'
];

// Types MIME autorisés
const ALLOWED_MIME_TYPES = [
    'image/jpeg',
    'image/png',
    'image/webp',
    'image/gif'
];
```

## Scopes

```php
// Images d'une collection spécifique
public function scopeInCollection($query, $collection)
{
    return $query->where('collection', $collection);
}

// Images optimisées
public function scopeOptimized($query)
{
    return $query->where('is_optimized', true);
}

// Images par type de modèle
public function scopeOfType($query, $type)
{
    return $query->where('imageable_type', $type);
}

// Images ordonnées
public function scopeOrdered($query)
{
    return $query->orderBy('order');
}
```

## Méthodes

```php
// Génère l'URL de l'image
public function getUrl(string $conversion = ''): string
{
    if ($conversion) {
        $conversionPath = $this->conversions()
            ->where('name', $conversion)
            ->value('path');
            
        if ($conversionPath) {
            return Storage::disk($this->disk)->url($conversionPath);
        }
    }
    
    return Storage::disk($this->disk)->url($this->path);
}

// Optimise l'image
public function optimize()
{
    if ($this->is_optimized) {
        return true;
    }

    try {
        $optimizer = app(ImageOptimizer::class);
        $path = Storage::disk($this->disk)->path($this->path);
        
        $optimizer->optimize($path);
        
        $this->update([
            'size' => filesize($path),
            'is_optimized' => true
        ]);

        return true;
    } catch (\Exception $e) {
        report($e);
        return false;
    }
}

// Crée une conversion
public function createConversion(string $name, array $manipulations)
{
    $converter = app(ImageConverter::class);
    
    $conversionPath = sprintf(
        'conversions/%s/%s-%s.%s',
        $name,
        $this->id,
        Str::random(10),
        pathinfo($this->filename, PATHINFO_EXTENSION)
    );

    $result = $converter->convert(
        Storage::disk($this->disk)->path($this->path),
        $conversionPath,
        $manipulations
    );

    if ($result) {
        return $this->conversions()->create([
            'name' => $name,
            'path' => $conversionPath
        ]);
    }

    return null;
}

// Supprime l'image et ses conversions
public function deleteWithConversions()
{
    DB::transaction(function () {
        // Supprime les fichiers de conversion
        $this->conversions->each(function ($conversion) {
            Storage::disk($this->disk)->delete($conversion->path);
        });

        // Supprime le fichier original
        Storage::disk($this->disk)->delete($this->path);

        // Supprime les enregistrements
        $this->conversions()->delete();
        $this->delete();
    });
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => ImageCreated::class,
    'deleted' => ImageDeleted::class
];
```

## Observers

```php
class ImageObserver
{
    public function created(Image $image)
    {
        if (config('images.auto_optimize')) {
            OptimizeImage::dispatch($image);
        }
    }

    public function deleted(Image $image)
    {
        $image->deleteWithConversions();
    }
}
```

## Validation

```php
class ImageValidator
{
    public static function rules()
    {
        return [
            'image' => [
                'required',
                'image',
                Rule::in(Image::ALLOWED_MIME_TYPES),
                'max:' . config('images.max_size', 5120)
            ],
            'collection' => [
                'required',
                Rule::in(Image::COLLECTIONS)
            ],
            'custom_properties' => 'nullable|array'
        ];
    }
}
```

## Notes de Sécurité

- Validation des types MIME
- Limitation de la taille des fichiers
- Vérification des permissions
- Sécurisation des chemins de stockage
- Protection contre les injections
- Validation des conversions
- Gestion sécurisée du stockage
- Nettoyage des métadonnées 