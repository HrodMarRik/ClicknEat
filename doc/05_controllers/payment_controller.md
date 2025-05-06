# PaymentController

`App\Http\Controllers\PaymentController`

Ce contrôleur gère toutes les opérations liées aux paiements, incluant l'intégration avec Stripe et la gestion des transactions.

## Dépendances

```php
use App\Services\PaymentService;
use App\Services\OrderService;
use Stripe\StripeClient;
use App\Events\PaymentProcessed;
```

## Méthodes

### createPaymentIntent()

Crée une intention de paiement Stripe.

```php
public function createPaymentIntent(PaymentIntentRequest $request)
{
    try {
        $order = $this->orderService->getOrderById($request->order_id);
        
        $paymentIntent = $this->paymentService->createIntent([
            'amount' => $order->total * 100, // Conversion en centimes
            'currency' => 'eur',
            'customer' => auth()->user()->stripe_customer_id,
            'metadata' => [
                'order_id' => $order->id,
                'restaurant_id' => $order->restaurant_id
            ]
        ]);

        return response()->json([
            'clientSecret' => $paymentIntent->client_secret
        ]);

    } catch (\Exception $e) {
        logger()->error('Payment intent creation failed: ' . $e->getMessage());
        
        return response()->json([
            'error' => 'Unable to create payment intent'
        ], 500);
    }
}
```

### processPayment()

Traite le paiement après confirmation.

```php
public function processPayment(ProcessPaymentRequest $request)
{
    try {
        $payment = $this->paymentService->processPayment(
            $request->payment_intent_id,
            $request->order_id
        );

        if ($payment->status === 'succeeded') {
            $this->orderService->confirmOrder($request->order_id);
            
            event(new PaymentProcessed($payment));

            return response()->json([
                'status' => 'success',
                'payment' => $payment
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Payment failed'
        ], 422);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
}
```

### handleWebhook()

Gère les webhooks Stripe.

```php
public function handleWebhook(Request $request)
{
    $payload = $request->all();
    $sigHeader = $request->header('Stripe-Signature');

    try {
        $event = $this->paymentService->constructWebhookEvent(
            $payload,
            $sigHeader
        );

        switch ($event->type) {
            case 'payment_intent.succeeded':
                $this->handleSuccessfulPayment($event->data->object);
                break;
                
            case 'payment_intent.payment_failed':
                $this->handleFailedPayment($event->data->object);
                break;
                
            case 'charge.refunded':
                $this->handleRefund($event->data->object);
                break;
        }

        return response()->json(['status' => 'processed']);

    } catch (\Exception $e) {
        logger()->error('Webhook error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()], 400);
    }
}
```

### savePaymentMethod()

Enregistre une nouvelle méthode de paiement.

```php
public function savePaymentMethod(SavePaymentMethodRequest $request)
{
    try {
        $paymentMethod = $this->paymentService->attachPaymentMethod(
            auth()->user(),
            $request->payment_method_id
        );

        return response()->json([
            'status' => 'success',
            'payment_method' => $paymentMethod
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to save payment method'
        ], 422);
    }
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth')->except('handleWebhook');
    $this->middleware('verified');
    $this->middleware('throttle:60,1');
}
```

## Validation

```php
class ProcessPaymentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'payment_intent_id' => 'required|string',
            'order_id' => 'required|exists:orders,id',
            'payment_method_id' => 'required|string'
        ];
    }
}
```

## Services

```php
class PaymentService
{
    private $stripe;

    public function __construct(StripeClient $stripe)
    {
        $this->stripe = $stripe;
    }

    public function createIntent(array $data)
    {
        return $this->stripe->paymentIntents->create($data);
    }

    public function constructWebhookEvent($payload, $sigHeader)
    {
        return Stripe\Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.webhook_secret')
        );
    }
}
```

## Events

- `PaymentProcessed`
- `PaymentFailed`
- `RefundProcessed`
- `PaymentMethodAdded`

## Exception Handling

```php
protected function handlePaymentException(\Exception $e)
{
    logger()->error('Payment error: ' . $e->getMessage());

    if ($e instanceof \Stripe\Exception\CardException) {
        return response()->json([
            'error' => 'Your card was declined'
        ], 422);
    }

    return response()->json([
        'error' => 'An error occurred while processing your payment'
    ], 500);
}
```

## Notes de Sécurité

- Validation des signatures Stripe
- Vérification des montants
- Protection contre les doubles paiements
- Logging sécurisé des transactions
- Gestion des erreurs de paiement
- Conformité PCI DSS 
