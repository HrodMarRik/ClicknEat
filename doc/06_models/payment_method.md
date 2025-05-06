# PaymentMethod Model

`App\Models\PaymentMethod`

Ce modèle représente les méthodes de paiement enregistrées par les utilisateurs.

## Structure de la Table

```php
Schema::create('payment_methods', function (Blueprint $table) {
    $table->id();
    $table->foreignId('user_id')->constrained();
    $table->string('type');
    $table->string('provider');
    $table->string('token')->unique();
    $table->string('last_four');
    $table->string('brand')->nullable();
    $table->string('holder_name')->nullable();
    $table->date('expiry_date')->nullable();
    $table->boolean('is_default')->default(false);
    $table->boolean('is_verified')->default(false);
    $table->json('meta_data')->nullable();
    $table->timestamp('last_used_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class PaymentMethod extends Model
{
    // Appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // A plusieurs transactions
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // A plusieurs factures
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
    'provider',
    'token',
    'last_four',
    'brand',
    'holder_name',
    'expiry_date',
    'is_default',
    'meta_data'
];

protected $casts = [
    'expiry_date' => 'date',
    'is_default' => 'boolean',
    'is_verified' => 'boolean',
    'meta_data' => 'array',
    'last_used_at' => 'datetime'
];

protected $hidden = [
    'token',
    'meta_data'
];

// Types de méthodes de paiement
const TYPES = [
    'CREDIT_CARD' => 'credit_card',
    'DEBIT_CARD' => 'debit_card',
    'PAYPAL' => 'paypal',
    'APPLE_PAY' => 'apple_pay',
    'GOOGLE_PAY' => 'google_pay'
];

// Fournisseurs de paiement
const PROVIDERS = [
    'STRIPE' => 'stripe',
    'PAYPAL' => 'paypal',
    'ADYEN' => 'adyen'
];
```

## Scopes

```php
// Méthodes de paiement vérifiées
public function scopeVerified($query)
{
    return $query->where('is_verified', true);
}

// Méthodes de paiement par type
public function scopeOfType($query, $type)
{
    return $query->where('type', $type);
}

// Méthodes de paiement par fournisseur
public function scopeByProvider($query, $provider)
{
    return $query->where('provider', $provider);
}

// Méthodes de paiement non expirées
public function scopeValid($query)
{
    return $query->where(function ($q) {
        $q->whereNull('expiry_date')
          ->orWhere('expiry_date', '>', now());
    });
}
```

## Méthodes

```php
// Vérifie si la méthode de paiement est valide
public function isValid(): bool
{
    if (!$this->is_verified) {
        return false;
    }

    if ($this->expiry_date && $this->expiry_date->isPast()) {
        return false;
    }

    return true;
}

// Définit comme méthode par défaut
public function setAsDefault()
{
    DB::transaction(function () {
        $this->user->paymentMethods()
            ->where('id', '!=', $this->id)
            ->update(['is_default' => false]);
            
        $this->update(['is_default' => true]);
    });
}

// Masque le numéro de carte
public function getMaskedNumberAttribute(): string
{
    return str_repeat('*', 12) . $this->last_four;
}

// Formate la date d'expiration
public function getFormattedExpiryAttribute(): ?string
{
    return $this->expiry_date
        ? $this->expiry_date->format('m/y')
        : null;
}

// Met à jour les détails de paiement via Stripe
public function updateStripeDetails(array $stripeData)
{
    $this->update([
        'brand' => $stripeData['card']['brand'],
        'last_four' => $stripeData['card']['last4'],
        'expiry_date' => Carbon::createFromDate(
            $stripeData['card']['exp_year'],
            $stripeData['card']['exp_month'],
            1
        ),
        'meta_data' => array_merge(
            $this->meta_data ?? [],
            ['stripe_data' => $stripeData]
        )
    ]);
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => PaymentMethodCreated::class,
    'deleted' => PaymentMethodDeleted::class
];
```

## Observers

```php
class PaymentMethodObserver
{
    public function created(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->user->paymentMethods()->count() === 1) {
            $paymentMethod->setAsDefault();
        }
    }

    public function deleted(PaymentMethod $paymentMethod)
    {
        if ($paymentMethod->is_default) {
            $newDefault = $paymentMethod->user
                ->paymentMethods()
                ->where('id', '!=', $paymentMethod->id)
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
class PaymentMethodValidator
{
    public static function rules()
    {
        return [
            'type' => ['required', Rule::in(PaymentMethod::TYPES)],
            'provider' => ['required', Rule::in(PaymentMethod::PROVIDERS)],
            'token' => 'required|string|unique:payment_methods',
            'holder_name' => 'required|string|max:255',
            'expiry_date' => 'nullable|date|after:today'
        ];
    }
}
```

## Notes de Sécurité

- Chiffrement des tokens
- Masquage des données sensibles
- Validation des dates d'expiration
- Vérification des permissions
- Protection contre les modifications non autorisées
- Logging des opérations sensibles
- Conformité PCI DSS
- Validation des fournisseurs de paiement 
