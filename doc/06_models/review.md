# Review Model

`App\Models\Review`

Ce modèle représente les avis laissés par les clients sur les restaurants et les commandes.

## Structure de la Table

```php
Schema::create('reviews', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('restaurant_id')->constrained();
    $table->foreignId('order_id')->constrained();
    $table->integer('rating');
    $table->integer('food_rating');
    $table->integer('delivery_rating')->nullable();
    $table->text('content')->nullable();
    $table->json('images')->nullable();
    $table->boolean('is_verified')->default(false);
    $table->boolean('is_featured')->default(false);
    $table->boolean('is_anonymous')->default(false);
    $table->timestamp('moderated_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Review extends Model
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

    // Appartient à une commande
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // A plusieurs réponses
    public function responses()
    {
        return $this->hasMany(ReviewResponse::class);
    }

    // A plusieurs signalements
    public function reports()
    {
        return $this->hasMany(ReviewReport::class);
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
    'user_id',
    'restaurant_id',
    'order_id',
    'rating',
    'food_rating',
    'delivery_rating',
    'content',
    'images',
    'is_anonymous',
];

protected $casts = [
    'rating' => 'integer',
    'food_rating' => 'integer',
    'delivery_rating' => 'integer',
    'images' => 'array',
    'is_verified' => 'boolean',
    'is_featured' => 'boolean',
    'is_anonymous' => 'boolean',
    'moderated_at' => 'datetime'
];

protected $hidden = [
    'user_id' => 'when:is_anonymous,true'
];
```

## Scopes

```php
// Avis vérifiés
public function scopeVerified($query)
{
    return $query->where('is_verified', true);
}

// Avis en vedette
public function scopeFeatured($query)
{
    return $query->where('is_featured', true);
}

// Avis récents
public function scopeRecent($query)
{
    return $query->orderBy('created_at', 'desc');
}

// Avis avec une note minimale
public function scopeMinRating($query, $rating)
{
    return $query->where('rating', '>=', $rating);
}

// Avis avec images
public function scopeWithImages($query)
{
    return $query->whereNotNull('images')
        ->where('images', '!=', '[]');
}
```

## Méthodes

```php
// Calcule la note moyenne
public function calculateAverageRating(): float
{
    $ratings = array_filter([
        $this->rating,
        $this->food_rating,
        $this->delivery_rating
    ]);
    
    return round(array_sum($ratings) / count($ratings), 1);
}

// Ajoute une réponse du restaurant
public function addResponse(string $content, User $responder)
{
    return $this->responses()->create([
        'content' => $content,
        'user_id' => $responder->id,
        'restaurant_id' => $this->restaurant_id
    ]);
}

// Signale un avis inapproprié
public function report(string $reason, User $reporter)
{
    return $this->reports()->create([
        'reason' => $reason,
        'user_id' => $reporter->id
    ]);
}

// Modère l'avis
public function moderate(bool $isApproved, ?string $reason = null)
{
    $this->is_verified = $isApproved;
    $this->moderated_at = now();
    $this->moderation_reason = $reason;
    $this->save();

    event(new ReviewModerated($this));
}

// Anonymise l'avis
public function anonymize()
{
    $this->is_anonymous = true;
    $this->save();
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => ReviewCreated::class,
    'updated' => ReviewUpdated::class
];
```

## Observers

```php
class ReviewObserver
{
    public function created(Review $review)
    {
        $review->restaurant->updateAverageRating();
        Cache::tags(['restaurant_reviews'])->flush();
    }

    public function deleted(Review $review)
    {
        foreach ($review->images as $image) {
            Storage::delete($image);
        }
    }
}
```

## Validation

```php
class ReviewValidator
{
    public static function rules()
    {
        return [
            'rating' => 'required|integer|between:1,5',
            'food_rating' => 'required|integer|between:1,5',
            'delivery_rating' => 'nullable|integer|between:1,5',
            'content' => 'nullable|string|min:10|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'images' => 'array|max:5'
        ];
    }
}
```

## Notes de Sécurité

- Validation des notes
- Protection contre le spam
- Modération du contenu
- Vérification des permissions
- Anonymisation des données utilisateur
- Sécurisation des images
- Protection contre les avis multiples
- Rate limiting 
