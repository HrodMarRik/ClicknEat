# UserController

`App\Http\Controllers\UserController`

Ce contrôleur gère les opérations liées aux utilisateurs, incluant la gestion des profils, préférences et adresses.

## Dépendances

```php
use App\Services\UserService;
use App\Repositories\UserRepository;
use App\Services\AddressService;
use App\Events\UserProfileUpdated;
```

## Méthodes

### show()

Affiche le profil de l'utilisateur.

```php
public function show()
{
    $user = auth()->user()->load([
        'addresses',
        'preferences',
        'orders' => function ($query) {
            $query->recent()->with('restaurant');
        }
    ]);

    return view('users.profile', compact('user'));
}
```

### update()

Met à jour les informations du profil utilisateur.

```php
public function update(UpdateProfileRequest $request)
{
    try {
        $user = $this->userService->updateProfile(
            auth()->user(),
            $request->validated()
        );

        if ($request->hasFile('avatar')) {
            $this->userService->updateAvatar(
                $user,
                $request->file('avatar')
            );
        }

        event(new UserProfileUpdated($user));

        return response()->json([
            'status' => 'success',
            'user' => $user
        ]);

    } catch (\Exception $e) {
        logger()->error('Profile update failed: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update profile'
        ], 422);
    }
}
```

### updatePreferences()

Met à jour les préférences alimentaires et de notification.

```php
public function updatePreferences(UpdatePreferencesRequest $request)
{
    try {
        $preferences = $this->userService->updatePreferences(
            auth()->user(),
            [
                'dietary_restrictions' => $request->dietary_restrictions,
                'favorite_cuisines' => $request->favorite_cuisines,
                'notification_preferences' => $request->notification_preferences
            ]
        );

        return response()->json([
            'status' => 'success',
            'preferences' => $preferences
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update preferences'
        ], 422);
    }
}
```

### manageAddresses()

Gère les adresses de livraison de l'utilisateur.

```php
public function manageAddresses(AddressRequest $request)
{
    try {
        $address = $this->addressService->createOrUpdateAddress(
            auth()->user(),
            [
                'title' => $request->title,
                'street' => $request->street,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'country' => $request->country,
                'is_default' => $request->boolean('is_default'),
                'instructions' => $request->instructions
            ]
        );

        return response()->json([
            'status' => 'success',
            'address' => $address
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to manage address'
        ], 422);
    }
}
```

### deleteAddress()

Supprime une adresse de livraison.

```php
public function deleteAddress(Address $address)
{
    $this->authorize('delete', $address);

    try {
        $this->addressService->deleteAddress($address);

        return response()->json([
            'status' => 'success',
            'message' => 'Address deleted successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete address'
        ], 422);
    }
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('verified')->only(['update', 'updatePreferences']);
    $this->middleware('throttle:60,1')->only(['update', 'manageAddresses']);
}
```

## Validation

Règles de validation pour la mise à jour du profil :

```php
class UpdateProfileRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:1024',
            'language' => 'nullable|string|in:' . implode(',', config('app.available_languages')),
            'timezone' => 'nullable|string|timezone'
        ];
    }
}
```

## Events

- `UserProfileUpdated`
- `UserPreferencesUpdated`
- `UserAddressCreated`
- `UserAddressUpdated`
- `UserAddressDeleted`

## Notifications

```php
class UserService
{
    private function sendProfileUpdateNotification(User $user)
    {
        $user->notify(new ProfileUpdatedNotification());
    }
}
```

## Cache

```php
class UserService
{
    private function clearUserCache($userId)
    {
        Cache::tags(['user_profile'])->forget("user:{$userId}");
        Cache::tags(['user_preferences'])->forget("preferences:{$userId}");
    }
}
```

## Notes de Sécurité

- Validation des données utilisateur
- Protection contre les attaques XSS
- Vérification des permissions
- Rate limiting sur les actions sensibles
- Gestion sécurisée des fichiers uploadés
- Hachage des données sensibles 
