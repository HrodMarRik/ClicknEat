# MenuController

`App\Http\Controllers\MenuController`

Ce contrôleur gère toutes les opérations liées aux menus des restaurants, incluant la gestion des catégories et des plats.

## Dépendances

```php
use App\Services\MenuService;
use App\Repositories\MenuRepository;
use App\Events\MenuUpdated;
use App\Models\{Menu, MenuItem, Category};
```

## Méthodes

### index()

Affiche le menu complet d'un restaurant avec ses catégories et plats.

```php
public function index(Restaurant $restaurant)
{
    $menu = $this->menuRepository->getFullMenu(
        $restaurant->id,
        ['categories.items', 'activePromotions']
    );

    return view('menus.index', [
        'menu' => $menu,
        'restaurant' => $restaurant,
        'categories' => $menu->categories
    ]);
}
```

### store()

Crée un nouveau plat dans le menu.

```php
public function store(CreateMenuItemRequest $request, Restaurant $restaurant)
{
    $this->authorize('manage', $restaurant);

    try {
        $menuItem = $this->menuService->createMenuItem([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'category_id' => $request->category_id,
            'is_available' => $request->boolean('is_available'),
            'preparation_time' => $request->preparation_time,
            'allergens' => $request->allergens,
            'nutritional_info' => $request->nutritional_info
        ]);

        if ($request->hasFile('image')) {
            $this->menuService->handleItemImage($menuItem, $request->file('image'));
        }

        event(new MenuUpdated($restaurant->id));

        return response()->json([
            'status' => 'success',
            'item' => $menuItem
        ], 201);

    } catch (\Exception $e) {
        logger()->error('Menu item creation failed: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create menu item'
        ], 500);
    }
}
```

### update()

Met à jour un plat existant.

```php
public function update(UpdateMenuItemRequest $request, MenuItem $menuItem)
{
    $this->authorize('update', $menuItem);

    try {
        $updatedItem = $this->menuService->updateMenuItem(
            $menuItem,
            $request->validated()
        );

        if ($request->hasFile('image')) {
            $this->menuService->handleItemImage($updatedItem, $request->file('image'));
        }

        event(new MenuUpdated($menuItem->restaurant_id));

        return response()->json([
            'status' => 'success',
            'item' => $updatedItem
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 422);
    }
}
```

### manageCategories()

Gère les catégories du menu.

```php
public function manageCategories(ManageCategoriesRequest $request, Restaurant $restaurant)
{
    $this->authorize('manage', $restaurant);

    try {
        $categories = $this->menuService->updateCategories(
            $restaurant->id,
            $request->categories
        );

        return response()->json([
            'status' => 'success',
            'categories' => $categories
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update categories'
        ], 500);
    }
}
```

### toggleAvailability()

Change la disponibilité d'un plat.

```php
public function toggleAvailability(MenuItem $menuItem)
{
    $this->authorize('update', $menuItem);
    
    $menuItem->is_available = !$menuItem->is_available;
    $menuItem->save();

    event(new MenuUpdated($menuItem->restaurant_id));

    return response()->json([
        'status' => 'success',
        'is_available' => $menuItem->is_available
    ]);
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth')->except(['index', 'show']);
    $this->middleware('restaurant.owner')->only([
        'store', 'update', 'destroy', 'manageCategories'
    ]);
}
```

## Validation

Règles de validation pour la création d'un plat :

```php
class CreateMenuItemRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'is_available' => 'boolean',
            'preparation_time' => 'required|integer|min:1',
            'allergens' => 'nullable|array',
            'allergens.*' => 'string|in:' . implode(',', config('menu.allergens')),
            'nutritional_info' => 'nullable|array',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ];
    }
}
```

## Events

- `MenuUpdated` : Déclenché lors de modifications du menu
- `MenuItemCreated` : Lors de la création d'un nouveau plat
- `MenuItemUpdated` : Lors de la mise à jour d'un plat
- `CategoryUpdated` : Lors de modifications des catégories

## Cache

```php
class MenuService
{
    private function clearMenuCache($restaurantId)
    {
        Cache::tags(['restaurant_menu'])->forget("menu:{$restaurantId}");
        Cache::tags(['menu_items'])->forget("items:{$restaurantId}");
    }
}
```

## Notes de Sécurité

- Validation des images
- Vérification des permissions via Policies
- Sanitization des entrées utilisateur
- Protection CSRF
- Gestion sécurisée des fichiers uploadés 
