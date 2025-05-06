# User Model

`App\Models\User`

Ce modèle représente les utilisateurs de l'application, incluant les clients, les restaurateurs et les administrateurs.

## Structure de la Table

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('phone')->nullable();
    $table->string('password');
    $table->enum('role', ['client', 'restaurant_owner', 'admin', 'delivery_driver']);
    $table->string('avatar')->nullable();
    $table->json('preferences')->nullable();
    $table->timestamp('email_verified_at')->nullable();
    $table->rememberToken();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class User extends Authenticatable
{
    // Un utilisateur peut avoir plusieurs commandes
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Un utilisateur peut avoir plusieurs adresses
    public function addresses()
    {
        return $this->hasMany(Address::class);
    }

    // Un utilisateur peut avoir un restaurant (si restaurateur)
    public function restaurant()
    {
        return $this->hasOne(Restaurant::class, 'owner_id');
    }

    // Un utilisateur peut avoir plusieurs avis
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    // Un utilisateur peut avoir plusieurs méthodes de paiement
    public function paymentMethods()
    {
        return $this->hasMany(PaymentMethod::class);
    }
}
```

## Attributs

```php
protected $fillable = [
    'name',
    'email',
    'phone',
    'password',
    'role',
    'preferences'
];

protected $hidden = [
    'password',
    'remember_token',
];

protected $casts = [
    'email_verified_at' => 'datetime',
    'preferences' => 'array',
];
```

## Accesseurs et Mutateurs

```php
// Récupère l'avatar avec une URL complète
public function getAvatarUrlAttribute()
{
    return $this->avatar
        ? Storage::url($this->avatar)
        : 'https://ui-avatars.com/api/?name=' . urlencode($this->name);
}

// Formate le numéro de téléphone
public function setPhoneAttribute($value)
{
    $this->attributes['phone'] = preg_replace('/[^0-9+]/', '', $value);
}
```

## Scopes

```php
// Filtre les utilisateurs par rôle
public function scopeByRole($query, $role)
{
    return $query->where('role', $role);
}

// Filtre les utilisateurs vérifiés
public function scopeVerified($query)
{
    return $query->whereNotNull('email_verified_at');
}

// Filtre les utilisateurs actifs récemment
public function scopeRecentlyActive($query, $days = 30)
{
    return $query->where('last_activity_at', '>=', now()->subDays($days));
}
```

## Méthodes

```php
// Vérifie si l'utilisateur est un administrateur
public function isAdmin(): bool
{
    return $this->role === 'admin';
}

// Vérifie si l'utilisateur est un restaurateur
public function isRestaurantOwner(): bool
{
    return $this->role === 'restaurant_owner';
}

// Vérifie si l'utilisateur peut laisser un avis
public function canReview(Order $order): bool
{
    return $this->id === $order->user_id && 
           $order->isDelivered() && 
           !$order->hasReview();
}

// Met à jour les préférences utilisateur
public function updatePreferences(array $preferences)
{
    $this->preferences = array_merge(
        $this->preferences ?? [],
        $preferences
    );
    $this->save();
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => UserCreated::class,
    'updated' => UserUpdated::class,
    'deleted' => UserDeleted::class,
];
```

## Notifications

```php
// Notifications que l'utilisateur peut recevoir
public function notifications()
{
    return [
        'order_status' => OrderStatusNotification::class,
        'promotion' => PromotionNotification::class,
        'account' => AccountNotification::class,
    ];
}
```

## Policies

Les autorisations sont gérées via `UserPolicy` :

```php
class UserPolicy
{
    public function update(User $user, User $target)
    {
        return $user->id === $target->id || $user->isAdmin();
    }

    public function delete(User $user, User $target)
    {
        return $user->isAdmin();
    }
}
```

## Notes de Sécurité

- Les mots de passe sont automatiquement hachés
- Les données sensibles sont masquées
- Validation des emails uniques
- Protection contre les attaques par force brute
- Vérification des rôles pour les actions sensibles 
