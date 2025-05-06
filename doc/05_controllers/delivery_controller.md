# DeliveryController

`App\Http\Controllers\DeliveryController`

Ce contrôleur gère toutes les opérations liées aux livraisons, incluant le suivi des livreurs et la gestion des statuts de livraison.

## Dépendances

```php
use App\Services\DeliveryService;
use App\Services\GeolocationService;
use App\Events\DeliveryStatusUpdated;
use App\Models\{Delivery, Order, DeliveryDriver};
```

## Méthodes

### track()

Permet de suivre une livraison en temps réel.

```php
public function track(Order $order)
{
    try {
        $this->authorize('track', $order);

        $delivery = $this->deliveryService->getDeliveryDetails(
            $order->delivery_id,
            ['driver', 'currentLocation']
        );

        return response()->json([
            'status' => 'success',
            'delivery' => $delivery,
            'estimated_time' => $delivery->estimated_arrival_time,
            'current_location' => $delivery->driver->current_location
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Unable to track delivery'
        ], 422);
    }
}
```

### updateStatus()

Met à jour le statut d'une livraison.

```php
public function updateStatus(UpdateDeliveryStatusRequest $request, Delivery $delivery)
{
    try {
        $this->authorize('update', $delivery);

        $updatedDelivery = $this->deliveryService->updateDeliveryStatus(
            $delivery,
            [
                'status' => $request->status,
                'location' => $request->location,
                'notes' => $request->notes
            ]
        );

        event(new DeliveryStatusUpdated($updatedDelivery));

        return response()->json([
            'status' => 'success',
            'delivery' => $updatedDelivery
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update delivery status'
        ], 422);
    }
}
```

### assignDriver()

Assigne un livreur à une livraison.

```php
public function assignDriver(AssignDriverRequest $request, Delivery $delivery)
{
    try {
        $this->authorize('manage', $delivery);

        $driver = DeliveryDriver::findOrFail($request->driver_id);
        
        $assignment = $this->deliveryService->assignDelivery(
            $delivery,
            $driver,
            [
                'estimated_pickup_time' => $request->estimated_pickup_time,
                'notes' => $request->notes
            ]
        );

        return response()->json([
            'status' => 'success',
            'assignment' => $assignment
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to assign driver'
        ], 422);
    }
}
```

### updateLocation()

Met à jour la position du livreur.

```php
public function updateLocation(UpdateLocationRequest $request)
{
    try {
        $driver = auth()->user()->driver;
        
        $location = $this->deliveryService->updateDriverLocation(
            $driver,
            [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'accuracy' => $request->accuracy
            ]
        );

        return response()->json([
            'status' => 'success',
            'location' => $location
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update location'
        ], 422);
    }
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('driver')->only(['updateLocation', 'updateStatus']);
    $this->middleware('throttle:60,1')->only(['updateLocation']);
}
```

## Validation

```php
class UpdateDeliveryStatusRequest extends FormRequest
{
    public function rules()
    {
        return [
            'status' => [
                'required',
                'string',
                Rule::in([
                    'pending',
                    'assigned',
                    'picked_up',
                    'in_transit',
                    'delivered',
                    'failed'
                ])
            ],
            'location' => 'required|array',
            'location.latitude' => 'required|numeric|between:-90,90',
            'location.longitude' => 'required|numeric|between:-180,180',
            'notes' => 'nullable|string|max:500'
        ];
    }
}
```

## Events

- `DeliveryStatusUpdated`
- `DeliveryAssigned`
- `DeliveryCompleted`
- `LocationUpdated`

## Services

```php
class DeliveryService
{
    public function calculateEstimatedTime($origin, $destination)
    {
        return $this->geolocationService->getEstimatedTravelTime(
            $origin,
            $destination,
            'driving'
        );
    }

    public function findNearestDriver($delivery)
    {
        return DeliveryDriver::available()
            ->whereNear($delivery->pickup_location)
            ->first();
    }
}
```

## Statuts de Livraison

```php
class DeliveryStatus
{
    const PENDING = 'pending';
    const ASSIGNED = 'assigned';
    const PICKED_UP = 'picked_up';
    const IN_TRANSIT = 'in_transit';
    const DELIVERED = 'delivered';
    const FAILED = 'failed';
}
```

## Notes de Sécurité

- Authentification requise
- Vérification des rôles (livreur)
- Validation des coordonnées GPS
- Rate limiting sur les mises à jour de position
- Protection contre le spoofing de localisation
- Vérification des permissions
- Logging des changements de statut 
