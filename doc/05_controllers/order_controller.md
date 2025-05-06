# OrderController

`App\Http\Controllers\OrderController`

Ce contrôleur gère toutes les opérations liées aux commandes des clients.

## Dépendances

```php
use App\Services\OrderService;
use App\Services\PaymentService;
use App\Repositories\OrderRepository;
use App\Events\OrderCreated;
```

## Méthodes

### index()

Affiche la liste des commandes de l'utilisateur connecté.

```php
public function index(Request $request)
{
    $orders = $this->orderRepository->getUserOrders(
        auth()->id(),
        $request->get('status'),
        10 // pagination
    );
    
    return view('orders.index', compact('orders'));
}
```

### store()

Crée une nouvelle commande.

```php
public function store(CreateOrderRequest $request)
{
    try {
        DB::beginTransaction();
        
        // Création de la commande
        $order = $this->orderService->createOrder([
            'user_id' => auth()->id(),
            'restaurant_id' => $request->restaurant_id,
            'items' => $request->items,
            'total' => $request->total,
            'delivery_address' => $request->delivery_address
        ]);

        // Traitement du paiement
        $payment = $this->paymentService->processPayment(
            $order,
            $request->payment_method_id
        );

        if ($payment->status === 'succeeded') {
            event(new OrderCreated($order));
            DB::commit();
            
            return response()->json([
                'status' => 'success',
                'order' => $order
            ]);
        }

        DB::rollBack();
        return response()->json([
            'status' => 'error',
            'message' => 'Payment failed'
        ], 422);

    } catch (\Exception $e) {
        DB::rollBack();
        logger()->error('Order creation failed: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'An error occurred'
        ], 500);
    }
}
```

### show()

Affiche les détails d'une commande spécifique.

```php
public function show(Order $order)
{
    $this->authorize('view', $order);
    
    return view('orders.show', [
        'order' => $order->load([
            'items.product',
            'restaurant',
            'deliveryAddress'
        ])
    ]);
}
```

### track()

Permet de suivre l'état d'une commande en temps réel.

```php
public function track(Order $order)
{
    $this->authorize('view', $order);
    
    return view('orders.track', [
        'order' => $order->load('statusHistory'),
        'currentStatus' => $order->current_status,
        'estimatedDeliveryTime' => $order->estimated_delivery_time
    ]);
}
```

### cancel()

Annule une commande si elle est encore dans un état permettant l'annulation.

```php
public function cancel(Order $order)
{
    $this->authorize('cancel', $order);
    
    try {
        $this->orderService->cancelOrder($order);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Order cancelled successfully'
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
    $this->middleware('auth');
    $this->middleware('verified');
    $this->middleware('throttle:60,1')->only(['store', 'cancel']);
}
```

## Validation

Les règles de validation sont définies dans des Form Requests dédiés :

```php
class CreateOrderRequest extends FormRequest
{
    public function rules()
    {
        return [
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'delivery_address' => 'required|string',
            'payment_method_id' => 'required|string'
        ];
    }
}
```

## Events

Le contrôleur émet plusieurs événements :

- `OrderCreated` : Lorsqu'une nouvelle commande est créée
- `OrderCancelled` : Lorsqu'une commande est annulée
- `OrderStatusUpdated` : Lorsque le statut d'une commande change

## Exceptions

Le contrôleur gère les exceptions suivantes :

- `OrderNotFoundException`
- `PaymentFailedException`
- `InvalidOrderStatusException`
- `UnauthorizedOrderActionException`

## Notes de Sécurité

- Vérification des permissions via les Policies Laravel
- Validation des entrées utilisateur
- Protection CSRF
- Rate limiting sur les actions sensibles
- Transactions DB pour l'intégrité des données 
