# ReviewController

`App\Http\Controllers\ReviewController`

Ce contrôleur gère les avis et évaluations des clients pour les restaurants et les commandes.

## Dépendances

```php
use App\Services\ReviewService;
use App\Events\ReviewSubmitted;
use App\Models\{Review, Order, Restaurant};
use App\Notifications\ReviewNotification;
```

## Méthodes

### index()

Affiche les avis pour un restaurant spécifique.

```php
public function index(Restaurant $restaurant)
{
    $reviews = $this->reviewService->getRestaurantReviews(
        $restaurant->id,
        [
            'user',
            'order',
            'responses'
        ],
        10 // pagination
    );

    return view('reviews.index', [
        'restaurant' => $restaurant,
        'reviews' => $reviews,
        'averageRating' => $restaurant->average_rating,
        'ratingDistribution' => $restaurant->rating_distribution
    ]);
}
```

### store()

Crée un nouvel avis pour une commande.

```php
public function store(CreateReviewRequest $request, Order $order)
{
    try {
        $this->authorize('review', $order);

        $review = $this->reviewService->createReview([
            'order_id' => $order->id,
            'restaurant_id' => $order->restaurant_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
            'content' => $request->content,
            'delivery_rating' => $request->delivery_rating,
            'food_rating' => $request->food_rating,
            'images' => $request->file('images')
        ]);

        event(new ReviewSubmitted($review));

        return response()->json([
            'status' => 'success',
            'review' => $review->load('user')
        ], 201);

    } catch (\Exception $e) {
        logger()->error('Review creation failed: ' . $e->getMessage());
        
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to submit review'
        ], 422);
    }
}
```

### update()

Met à jour un avis existant.

```php
public function update(UpdateReviewRequest $request, Review $review)
{
    try {
        $this->authorize('update', $review);

        $updatedReview = $this->reviewService->updateReview(
            $review,
            $request->validated()
        );

        if ($request->hasFile('images')) {
            $this->reviewService->handleReviewImages(
                $updatedReview,
                $request->file('images')
            );
        }

        return response()->json([
            'status' => 'success',
            'review' => $updatedReview
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to update review'
        ], 422);
    }
}
```

### respond()

Permet au restaurant de répondre à un avis.

```php
public function respond(RespondToReviewRequest $request, Review $review)
{
    try {
        $this->authorize('respond', $review);

        $response = $this->reviewService->addResponse(
            $review,
            [
                'content' => $request->content,
                'restaurant_id' => auth()->user()->restaurant_id
            ]
        );

        $review->user->notify(new ReviewNotification($response));

        return response()->json([
            'status' => 'success',
            'response' => $response
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to add response'
        ], 422);
    }
}
```

### report()

Signale un avis inapproprié.

```php
public function report(ReportReviewRequest $request, Review $review)
{
    try {
        $report = $this->reviewService->reportReview(
            $review,
            [
                'reason' => $request->reason,
                'details' => $request->details,
                'reporter_id' => auth()->id()
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Review reported successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to report review'
        ], 422);
    }
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth')->except(['index', 'show']);
    $this->middleware('throttle:10,1')->only(['store', 'report']);
    $this->middleware('verified')->only(['store']);
}
```

## Validation

```php
class CreateReviewRequest extends FormRequest
{
    public function rules()
    {
        return [
            'rating' => 'required|integer|between:1,5',
            'content' => 'required|string|min:10|max:1000',
            'delivery_rating' => 'required|integer|between:1,5',
            'food_rating' => 'required|integer|between:1,5',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'images' => 'array|max:5'
        ];
    }
}
```

## Events

- `ReviewSubmitted`
- `ReviewUpdated`
- `ReviewResponded`
- `ReviewReported`

## Policies

```php
class ReviewPolicy
{
    public function update(User $user, Review $review)
    {
        return $user->id === $review->user_id && 
               $review->created_at->addHours(24)->isFuture();
    }

    public function respond(User $user, Review $review)
    {
        return $user->restaurant_id === $review->restaurant_id;
    }
}
```

## Notes de Sécurité

- Validation des images
- Protection contre le spam
- Modération des contenus
- Rate limiting
- Vérification des autorisations
- Protection CSRF
- Sanitization des entrées utilisateur 
