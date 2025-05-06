# RestaurantController

`App\Http\Controllers\RestaurantController`

Ce contrôleur gère les opérations liées aux restaurants, incluant l'affichage, la recherche et la gestion des restaurants.

## Dépendances

```php
use App\Services\RestaurantService;
use App\Repositories\RestaurantRepository;
use App\Services\GeolocationService;
use App\Events\RestaurantStatusUpdated;
```

## Méthodes

### index()

Affiche la liste des restaurants avec filtres et recherche.

```php
public function index(RestaurantSearchRequest $request)
{
    $filters = [
        'cuisine_type' => $request->get('cuisine'),
        'rating' => $request->get('rating'),
        'price_range' => $request->get('price'),
        'open_now' => $request->boolean('open_now')
    ];

    if ($request->has('location')) {
        $coordinates = $this->geolocationService->getCoordinates($request->location);
        $restaurants = $this->restaurantRepository->getNearby(
            $coordinates['lat'],
            $coordinates['lng'],
            $filters,
            15 // pagination
        );
    } else {
        $restaurants = $this->restaurantRepository->getAllActive($filters, 15);
    }

    return view('restaurants.index', compact('restaurants'));
}
```

### show()

Affiche les détails d'un restaurant spécifique.

```php
public function show(Restaurant $restaurant)
{
    $restaurant->load([
        'menu.categories.items',
        'reviews' => function ($query) {
            $query->latest()->take(5);
        },
        'openingHours'
    ]);

    $averageRating = $restaurant->reviews()->avg('rating');
    $isOpen = $restaurant->isCurrentlyOpen();

    return view('restaurants.show', compact(
        'restaurant',
        'averageRating',
        'isOpen'
    ));
}
```

### store()

Crée un nouveau restaurant (réservé aux administrateurs et aux restaurateurs).

```php
public function store(CreateRestaurantRequest $request)
{
    $this->authorize('create', Restaurant::class);

    try {
        $restaurant = $this->restaurantService->createRestaurant([
            'name' => $request->name,
            'description' => $request->description,
            'address' => $request->address,
            'cuisine_type' => $request->cuisine_type,
            'owner_id' => auth()->id(),
            'opening_hours' => $request->opening_hours,
            'delivery_radius' => $request->delivery_radius,
            'minimum_order' => $request->minimum_order
        ]);

        if ($request->hasFile('images')) {
            $this->restaurantService->handleImageUploads(
                $restaurant,
                $request->file('images')
            );
        }

        return response()->json([
            'status' => 'success',
            'restaurant' => $restaurant
        ], 201);

    } catch (\Exception $e) {
        logger()->error('Restaurant creation failed: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create restaurant'
        ], 500);
    }
}
```

### update()

Met à jour les informations d'un restaurant.

```php
public function update(UpdateRestaurantRequest $request, Restaurant $restaurant)
{
    $this->authorize('update', $restaurant);

    try {
        $restaurant = $this->restaurantService->updateRestaurant(
            $restaurant,
            $request->validated()
        );

        if ($request->has('status')) {
            event(new RestaurantStatusUpdated($restaurant));
        }

        return response()->json([
            'status' => 'success',
            'restaurant' => $restaurant
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 422);
    }
}
```

### toggleStatus()

Change le statut d'ouverture/fermeture d'un restaurant.

```php
public function toggleStatus(Restaurant $restaurant)
{
    $this->authorize('manage', $restaurant);
    
    $restaurant->is_open = !$restaurant->is_open;
    $restaurant->save();

    event(new RestaurantStatusUpdated($restaurant));

    return response()->json([
        'status' => 'success',
        'is_open' => $restaurant->is_open
    ]);
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth')->except(['index', 'show']);
    $this->middleware('verified')->only(['store', 'update', 'destroy']);
    $this->middleware('throttle:60,1')->only(['store', 'update']);
}
```

## Validation

Règles de validation pour la création d'un restaurant :

```php
class CreateRestaurantRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255|unique:restaurants',
            'description' => 'required|string|max:1000',
            'address' => 'required|string|max:255',
            'cuisine_type' => 'required|string|in:' . implode(',', config('restaurants.cuisine_types')),
            'opening_hours' => 'required|array',
            'opening_hours.*' => 'required|string',
            'delivery_radius' => 'required|numeric|min:1|max:50',
            'minimum_order' => 'required|numeric|min:0',
            'images.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}
```

## Events

- `RestaurantCreated`
- `RestaurantUpdated`
- `RestaurantStatusUpdated`
- `RestaurantDeleted`

## Policies

Les autorisations sont gérées via `RestaurantPolicy` :

```php
class RestaurantPolicy
{
    public function update(User $user, Restaurant $restaurant)
    {
        return $user->id === $restaurant->owner_id || $user->isAdmin();
    }

    public function manage(User $user, Restaurant $restaurant)
    {
        return $user->id === $restaurant->owner_id || $user->isAdmin();
    }
}
```

## Notes de Sécurité

- Validation des images uploadées
- Vérification des permissions
- Sanitization des entrées
- Protection CSRF
- Rate limiting sur les actions critiques 