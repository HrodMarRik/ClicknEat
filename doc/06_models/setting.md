# Setting Model

`App\Models\Setting`

Ce modèle représente les paramètres de configuration de l'application, permettant une gestion dynamique des réglages.

## Structure de la Table

```php
Schema::create('settings', function (Blueprint $table) {
    $table->id();
    $table->string('key')->unique();
    $table->text('value')->nullable();
    $table->string('type')->default('string');
    $table->string('group')->default('general');
    $table->text('description')->nullable();
    $table->json('options')->nullable();
    $table->boolean('is_public')->default(false);
    $table->boolean('is_system')->default(false);
    $table->string('validation_rules')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Setting extends Model
{
    // A plusieurs historiques de modifications
    public function history()
    {
        return $this->hasMany(SettingHistory::class);
    }

    // A plusieurs autorisations
    public function permissions()
    {
        return $this->belongsToMany(Role::class, 'setting_role');
    }
}
```

## Attributs

```php
protected $fillable = [
    'key',
    'value',
    'type',
    'group',
    'description',
    'options',
    'is_public',
    'is_system',
    'validation_rules'
];

protected $casts = [
    'value' => 'json',
    'options' => 'array',
    'is_public' => 'boolean',
    'is_system' => 'boolean'
];

// Types de paramètres
const TYPES = [
    'STRING' => 'string',
    'INTEGER' => 'integer',
    'FLOAT' => 'float',
    'BOOLEAN' => 'boolean',
    'ARRAY' => 'array',
    'JSON' => 'json',
    'DATE' => 'date',
    'TIME' => 'time',
    'DATETIME' => 'datetime',
    'FILE' => 'file',
    'IMAGE' => 'image'
];

// Groupes de paramètres
const GROUPS = [
    'GENERAL' => 'general',
    'DELIVERY' => 'delivery',
    'PAYMENT' => 'payment',
    'NOTIFICATION' => 'notification',
    'SECURITY' => 'security',
    'INTEGRATION' => 'integration',
    'APPEARANCE' => 'appearance'
];
```

## Scopes

```php
// Paramètres publics
public function scopePublic($query)
{
    return $query->where('is_public', true);
}

// Paramètres système
public function scopeSystem($query)
{
    return $query->where('is_system', true);
}

// Paramètres par groupe
public function scopeInGroup($query, $group)
{
    return $query->where('group', $group);
}

// Paramètres par type
public function scopeOfType($query, $type)
{
    return $query->where('type', $type);
}

// Paramètres modifiables
public function scopeEditable($query)
{
    return $query->where('is_system', false);
}
```

## Méthodes

```php
// Obtient la valeur typée
public function getTypedValue()
{
    $value = $this->value;

    switch ($this->type) {
        case self::TYPES['INTEGER']:
            return (int) $value;
        case self::TYPES['FLOAT']:
            return (float) $value;
        case self::TYPES['BOOLEAN']:
            return (bool) $value;
        case self::TYPES['ARRAY']:
        case self::TYPES['JSON']:
            return json_decode($value, true);
        case self::TYPES['DATE']:
            return Carbon::parse($value);
        case self::TYPES['DATETIME']:
            return Carbon::parse($value);
        default:
            return $value;
    }
}

// Met à jour la valeur
public function updateValue($value, User $user = null)
{
    $oldValue = $this->value;

    DB::transaction(function () use ($value, $user, $oldValue) {
        $this->update(['value' => $value]);

        $this->history()->create([
            'old_value' => $oldValue,
            'new_value' => $value,
            'user_id' => $user ? $user->id : null
        ]);

        Cache::tags('settings')->forget($this->key);
    });

    event(new SettingUpdated($this));
}

// Valide une valeur
public function validateValue($value): bool
{
    if (empty($this->validation_rules)) {
        return true;
    }

    $validator = Validator::make(
        ['value' => $value],
        ['value' => $this->validation_rules]
    );

    return !$validator->fails();
}

// Vérifie si une valeur est dans les options
public function isValidOption($value): bool
{
    if (empty($this->options)) {
        return true;
    }

    return in_array($value, $this->options);
}

// Obtient la valeur mise en cache
public static function get(string $key, $default = null)
{
    return Cache::tags('settings')->remember(
        "setting:{$key}",
        now()->addDay(),
        function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            return $setting ? $setting->getTypedValue() : $default;
        }
    );
}

// Définit une valeur
public static function set(string $key, $value, User $user = null)
{
    $setting = static::firstOrCreate(['key' => $key]);
    $setting->updateValue($value, $user);
    return $setting;
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => SettingCreated::class,
    'updated' => SettingUpdated::class
];
```

## Observers

```php
class SettingObserver
{
    public function saved(Setting $setting)
    {
        Cache::tags('settings')->flush();
    }

    public function deleted(Setting $setting)
    {
        if ($setting->is_system) {
            throw new \Exception("Cannot delete system settings");
        }
    }
}
```

## Validation

```php
class SettingValidator
{
    public static function rules()
    {
        return [
            'key' => 'required|string|max:255|unique:settings,key',
            'type' => ['required', Rule::in(Setting::TYPES)],
            'group' => ['required', Rule::in(Setting::GROUPS)],
            'description' => 'nullable|string',
            'options' => 'nullable|array',
            'is_public' => 'boolean',
            'is_system' => 'boolean',
            'validation_rules' => 'nullable|string'
        ];
    }
}
```

## Notes de Sécurité

- Validation des valeurs
- Protection des paramètres système
- Vérification des permissions
- Historique des modifications
- Cache sécurisé
- Validation des types
- Protection contre les injections
- Logging des changements 
