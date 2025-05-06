# CartController

`App\Http\Controllers\CartController`

Ce contrôleur gère toutes les opérations liées au panier d'achat, incluant l'ajout, la modification et la suppression d'articles.

## Dépendances

```php
use App\Services\CartService;
use App\Services\MenuService;
use App\Events\CartUpdated;
use App\Models\{Cart, CartItem};
```

## Méthodes

### show()

Affiche le contenu du panier actuel.

```php
public function show(Request $request)
{
    $cart = $this->cartService->getCurrentCart(
        auth()->id() ?? $request->session()->getId()
    );

    $cart->load([
        'items.product',
        'restaurant',
        'appliedPromotions'
    ]);

    return view('cart.show', [
        'cart' => $cart,
        'subtotal' => $cart->calculateSubtotal(),
        'total' => $cart->calculateTotal(),
        'deliveryFee' => $cart->calculateDeliveryFee()
    ]);
}
```

### addItem()

Ajoute un article au panier.

```php
public function addItem(AddToCartRequest $request)
{
    try {
        $cart = $this->cartService->addToCart(
            auth()->id() ?? $request->session()->getId(),
            [
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
                'special_instructions' => $request->special_instructions,
                'options' => $request->options
            ]
        );

        event(new CartUpdated($cart));

        return response()->json([
            'status' => 'success',
            'cart' => $cart,
            'message' => 'Item added to cart'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 422);
    }
}
```

### updateItem()

Met à jour la quantité d'un article dans le panier.

```php
public function updateItem(UpdateCartItemRequest $request, CartItem $item)
{
    try {
        $this->authorize('update', $item);

        $updatedItem = $this->cartService->updateCartItem(
            $item,
            [
                'quantity' => $request->quantity,
                'special_instructions' => $request->special_instructions
            ]
        );

        $cart = $updatedItem->cart->fresh();
        event(new CartUpdated($cart));

        return response()->json([
            'status' => 'success',
            'item' => $updatedItem,
            'cart_total' => $cart->calculateTotal()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update cart item'
        ], 422);
    }
}
```

### removeItem()

Supprime un article du panier.

```php
public function removeItem(CartItem $item)
{
    try {
        $this->authorize('delete', $item);
        
        $cart = $item->cart;
        $this->cartService->removeCartItem($item);

        event(new CartUpdated($cart->fresh()));

        return response()->json([
            'status' => 'success',
            'message' => 'Item removed from cart'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to remove item'
        ], 422);
    }
}
```

### applyPromoCode()

Applique un code promotionnel au panier.

```php
public function applyPromoCode(PromoCodeRequest $request)
{
    try {
        $cart = $this->cartService->applyPromoCode(
            auth()->id() ?? $request->session()->getId(),
            $request->promo_code
        );

        return response()->json([
            'status' => 'success',
            'cart' => $cart,
            'discount' => $cart->calculateDiscount(),
            'total' => $cart->calculateTotal()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 422);
    }
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('web');
    $this->middleware('throttle:60,1')->only(['addItem', 'updateItem', 'applyPromoCode']);
}
```

## Validation

```php
class AddToCartRequest extends FormRequest
{
    public function rules()
    {
        return [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:99',
            'special_instructions' => 'nullable|string|max:255',
            'options' => 'nullable|array',
            'options.*' => 'exists:product_options,id'
        ];
    }
}
```

## Events

- `CartUpdated`
- `CartItemAdded`
- `CartItemRemoved`
- `PromoCodeApplied`

## Services

```php
class CartService
{
    public function getCurrentCart($identifier)
    {
        return Cart::firstOrCreate(
            ['identifier' => $identifier],
            ['expires_at' => now()->addDays(7)]
        );
    }

    public function validateRestaurantConsistency($cart, $productId)
    {
        $product = Product::find($productId);
        
        if ($cart->items->isNotEmpty() && 
            $cart->restaurant_id !== $product->restaurant_id) {
            throw new \Exception('Cannot add items from different restaurants');
        }
    }
}
```

## Notes de Sécurité

- Validation des entrées utilisateur
- Protection contre les modifications non autorisées
- Vérification de la cohérence des restaurants
- Nettoyage automatique des paniers expirés
- Protection CSRF
- Rate limiting sur les actions sensibles 
