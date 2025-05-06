# PromotionController

`App\Http\Controllers\PromotionController`

Ce contrôleur gère toutes les opérations liées aux promotions, réductions et codes promotionnels.

## Dépendances

```php
use App\Services\PromotionService;
use App\Events\PromotionCreated;
use App\Models\{Promotion, Restaurant};
use App\Notifications\PromotionNotification;
```

## Méthodes

### index()

Affiche la liste des promotions actives.

```php
public function index(Request $request)
{
    $promotions = $this->promotionService->getActivePromotions(
        [
            'restaurant',
            'conditions',
            'usageStats'
        ],
        $request->get('type'),
        15 // pagination
    );

    return view('promotions.index', [
        'promotions' => $promotions,
        'types' => Promotion::TYPES,
        'stats' => $this->promotionService->getGlobalStats()
    ]);
}
```

### store()

Crée une nouvelle promotion.

```php
public function store(CreatePromotionRequest $request)
{
    try {
        $this->authorize('create', Promotion::class);

        $promotion = $this->promotionService->createPromotion([
            'code' => $request->code,
            'type' => $request->type,
            'value' => $request->value,
            'description' => $request->description,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'conditions' => $request->conditions,
            'restaurant_id' => auth()->user()->restaurant_id,
            'usage_limit' => $request->usage_limit,
            'minimum_order' => $request->minimum_order
        ]);

        event(new PromotionCreated($promotion));

        return response()->json([
            'status' => 'success',
            'promotion' => $promotion
        ], 201);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to create promotion'
        ], 422);
    }
}
```

### validate()

Valide un code promotionnel.

```php
public function validate(ValidatePromoRequest $request)
{
    try {
        $result = $this->promotionService->validatePromoCode(
            $request->code,
            [
                'user_id' => auth()->id(),
                'cart_total' => $request->cart_total,
                'restaurant_id' => $request->restaurant_id
            ]
        );

        return response()->json([
            'status' => 'success',
            'valid' => $result['valid'],
            'discount' => $result['discount'] ?? null,
            'message' => $result['message']
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 422);
    }
}
```

### update()

Met à jour une promotion existante.

```php
public function update(UpdatePromotionRequest $request, Promotion $promotion)
{
    try {
        $this->authorize('update', $promotion);

        $updatedPromotion = $this->promotionService->updatePromotion(
            $promotion,
            $request->validated()
        );

        return response()->json([
            'status' => 'success',
            'promotion' => $updatedPromotion
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update promotion'
        ], 422);
    }
}
```

### toggleStatus()

Active ou désactive une promotion.

```php
public function toggleStatus(Promotion $promotion)
{
    try {
        $this->authorize('update', $promotion);
        
        $promotion->is_active = !$promotion->is_active;
        $promotion->save();

        return response()->json([
            'status' => 'success',
            'is_active' => $promotion->is_active
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to toggle promotion status'
        ], 422);
    }
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth')->except(['index', 'validate']);
    $this->middleware('restaurant.owner')->only(['store', 'update', 'toggleStatus']);
    $this->middleware('throttle:60,1')->only(['validate']);
}
```

## Validation

```php
class CreatePromotionRequest extends FormRequest
{
    public function rules()
    {
        return [
            'code' => 'required|string|unique:promotions|max:20',
            'type' => [
                'required',
                Rule::in(['percentage', 'fixed_amount', 'free_delivery'])
            ],
            'value' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'conditions' => 'nullable|array',
            'usage_limit' => 'nullable|integer|min:1',
            'minimum_order' => 'nullable|numeric|min:0'
        ];
    }
}
```

## Types de Promotion

```php
class PromotionTypes
{
    const PERCENTAGE = 'percentage';
    const FIXED_AMOUNT = 'fixed_amount';
    const FREE_DELIVERY = 'free_delivery';
    
    public static function getCalculator($type)
    {
        return match($type) {
            self::PERCENTAGE => new PercentageCalculator(),
            self::FIXED_AMOUNT => new FixedAmountCalculator(),
            self::FREE_DELIVERY => new FreeDeliveryCalculator(),
        };
    }
}
```

## Events

- `PromotionCreated`
- `PromotionUpdated`
- `PromotionActivated`
- `PromotionDeactivated`
- `PromotionRedeemed`

## Notes de Sécurité

- Validation des dates
- Vérification des limites d'utilisation
- Protection contre les abus
- Vérification des permissions
- Rate limiting sur la validation
- Logging des utilisations
- Vérification des conditions d'application 
