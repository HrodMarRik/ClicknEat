# Promotion Model

`App\Models\Promotion`

Ce modèle représente les promotions et réductions applicables aux commandes et aux restaurants.

## Structure de la Table

```php
Schema::create('promotions', function (Blueprint $table) {
    $table->id();
    $table->foreignId('restaurant_id')->nullable()->constrained();
    $table->string('code')->unique();
    $table->string('type');
    $table->decimal('value', 8, 2);
    $table->string('description');
    $table->timestamp('start_date');
    $table->timestamp('end_date');
    $table->integer('usage_limit')->nullable();
    $table->integer('usage_count')->default(0);
    $table->decimal('minimum_order', 8, 2)->nullable();
    $table->json('conditions')->nullable();
    $table->boolean('is_active')->default(true);
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Promotion extends Model
{
    // Appartient à un restaurant
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    // A plusieurs utilisations
    public function usages()
    {
        return $this->hasMany(PromotionUsage::class);
    }

    // Applicable à plusieurs catégories
    public function categories()
    {
        return $this->belongsToMany(Category::class);
    }

    // Applicable à plusieurs produits
    public function items()
    {
        return $this->belongsToMany(MenuItem::class);
    }

    // A plusieurs restrictions
    public function restrictions()
    {
        return $this->hasMany(PromotionRestriction::class);
    }
}
```

## Attributs

```php
protected $fillable = [
    'restaurant_id',
    'code',
    'type',
    'value',
    'description',
    'start_date',
    'end_date',
    'usage_limit',
    'minimum_order',
    'conditions',
    'is_active'
];

protected $casts = [
    'value' => 'decimal:2',
    'minimum_order' => 'decimal:2',
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'conditions' => 'array',
    'is_active' => 'boolean'
];

// Types de promotion
const TYPES = [
    'PERCENTAGE' => 'percentage',
    'FIXED_AMOUNT' => 'fixed_amount',
    'FREE_DELIVERY' => 'free_delivery',
    'BUY_ONE_GET_ONE' => 'bogo'
];
```

## Scopes

```php
// Promotions actives
public function scopeActive($query)
{
    return $query->where('is_active', true)
        ->where('start_date', '<=', now())
        ->where('end_date', '>=', now())
        ->where(function ($q) {
            $q->whereNull('usage_limit')
              ->orWhereRaw('usage_count < usage_limit');
        });
}

// Promotions par type
public function scopeOfType($query, $type)
{
    return $query->where('type', $type);
}

// Promotions applicables à un montant minimum
public function scopeApplicableTo($query, $orderAmount)
{
    return $query->where(function ($q) use ($orderAmount) {
        $q->whereNull('minimum_order')
          ->orWhere('minimum_order', '<=', $orderAmount);
    });
}

// Promotions à venir
public function scopeUpcoming($query)
{
    return $query->where('start_date', '>', now());
}
```

## Méthodes

```php
// Calcule la réduction
public function calculateDiscount($amount): float
{
    switch ($this->type) {
        case self::TYPES['PERCENTAGE']:
            return round(($amount * $this->value) / 100, 2);
            
        case self::TYPES['FIXED_AMOUNT']:
            return min($this->value, $amount);
            
        case self::TYPES['FREE_DELIVERY']:
            return $this->restaurant->delivery_fee;
            
        default:
            return 0;
    }
}

// Vérifie si la promotion est valide
public function isValid(): bool
{
    return $this->is_active &&
           now()->between($this->start_date, $this->end_date) &&
           ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
}

// Applique la promotion à une commande
public function applyToOrder(Order $order): bool
{
    if (!$this->isValidForOrder($order)) {
        return false;
    }

    DB::transaction(function () use ($order) {
        $discount = $this->calculateDiscount($order->subtotal);
        
        $order->update([
            'discount' => $discount,
            'total' => $order->subtotal + $order->delivery_fee - $discount
        ]);

        $this->increment('usage_count');
        
        $this->usages()->create([
            'order_id' => $order->id,
            'user_id' => $order->user_id,
            'discount_amount' => $discount
        ]);
    });

    return true;
}

// Vérifie les conditions d'application
public function isValidForOrder(Order $order): bool
{
    if (!$this->isValid()) {
        return false;
    }

    if ($this->minimum_order && $order->subtotal < $this->minimum_order) {
        return false;
    }

    if ($this->restaurant_id && $order->restaurant_id !== $this->restaurant_id) {
        return false;
    }

    return $this->checkConditions($order);
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => PromotionCreated::class,
    'updated' => PromotionUpdated::class
];
```

## Observers

```php
class PromotionObserver
{
    public function creating(Promotion $promotion)
    {
        if (empty($promotion->code)) {
            $promotion->code = $this->generateUniqueCode();
        }
    }

    public function saved(Promotion $promotion)
    {
        Cache::tags(['promotions'])->flush();
    }

    protected function generateUniqueCode()
    {
        do {
            $code = strtoupper(Str::random(8));
        } while (Promotion::where('code', $code)->exists());

        return $code;
    }
}
```

## Cache

```php
public function getActivePromotions()
{
    return Cache::tags(['promotions'])
        ->remember('active_promotions', 3600, function () {
            return static::active()->get();
        });
}
```

## Notes de Sécurité

- Validation des dates
- Vérification des limites d'utilisation
- Protection contre les utilisations multiples
- Validation des montants
- Vérification des conditions
- Protection contre les codes frauduleux
- Rate limiting sur les validations
- Logging des utilisations 
