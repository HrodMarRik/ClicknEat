# NotificationController

`App\Http\Controllers\NotificationController`

Ce contrôleur gère toutes les notifications de l'application, incluant les notifications en temps réel et les préférences de notification.

## Dépendances

```php
use App\Services\NotificationService;
use App\Events\NotificationRead;
use App\Models\Notification;
use Illuminate\Support\Facades\Broadcast;
```

## Méthodes

### index()

Affiche toutes les notifications de l'utilisateur.

```php
public function index(Request $request)
{
    $notifications = $this->notificationService->getUserNotifications(
        auth()->user(),
        $request->get('type'),
        15 // pagination
    );

    return view('notifications.index', [
        'notifications' => $notifications,
        'unreadCount' => auth()->user()->unreadNotifications->count()
    ]);
}
```

### markAsRead()

Marque une notification comme lue.

```php
public function markAsRead(Notification $notification)
{
    try {
        $this->authorize('update', $notification);

        $notification->markAsRead();
        event(new NotificationRead($notification));

        return response()->json([
            'status' => 'success',
            'unreadCount' => auth()->user()->unreadNotifications->count()
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to mark notification as read'
        ], 422);
    }
}
```

### markAllAsRead()

Marque toutes les notifications comme lues.

```php
public function markAllAsRead()
{
    try {
        auth()->user()->unreadNotifications->markAsRead();

        return response()->json([
            'status' => 'success',
            'message' => 'All notifications marked as read'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to mark notifications as read'
        ], 422);
    }
}
```

### updatePreferences()

Met à jour les préférences de notification.

```php
public function updatePreferences(UpdateNotificationPreferencesRequest $request)
{
    try {
        $preferences = $this->notificationService->updatePreferences(
            auth()->user(),
            [
                'email_notifications' => $request->email_notifications,
                'push_notifications' => $request->push_notifications,
                'sms_notifications' => $request->sms_notifications,
                'notification_types' => $request->notification_types
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

### getUnreadCount()

Retourne le nombre de notifications non lues.

```php
public function getUnreadCount()
{
    return response()->json([
        'count' => auth()->user()->unreadNotifications->count()
    ]);
}
```

### delete()

Supprime une notification.

```php
public function delete(Notification $notification)
{
    try {
        $this->authorize('delete', $notification);
        
        $notification->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Notification deleted'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => 'Failed to delete notification'
        ], 422);
    }
}
```

## Middleware

```php
public function __construct()
{
    $this->middleware('auth');
    $this->middleware('throttle:60,1');
}
```

## Validation

```php
class UpdateNotificationPreferencesRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email_notifications' => 'boolean',
            'push_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'notification_types' => 'array',
            'notification_types.*' => 'string|in:order_updates,promotions,reviews,system'
        ];
    }
}
```

## Broadcast Channels

```php
Broadcast::channel('App.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

## Types de Notifications

```php
class NotificationTypes
{
    const ORDER_STATUS = 'order_status';
    const NEW_REVIEW = 'new_review';
    const PROMOTION = 'promotion';
    const SYSTEM = 'system';
    const PAYMENT = 'payment';
}
```

## Events

- `NotificationRead`
- `NotificationSent`
- `NotificationsCleared`
- `PreferencesUpdated`

## Services

```php
class NotificationService
{
    public function sendPushNotification($user, $data)
    {
        return $this->pushNotificationProvider->send(
            $user->device_tokens,
            [
                'title' => $data['title'],
                'body' => $data['body'],
                'data' => $data['extra'] ?? []
            ]
        );
    }

    public function shouldSendNotification($user, $type)
    {
        $preferences = $user->notification_preferences;
        return $preferences[$type] ?? true;
    }
}
```

## Notes de Sécurité

- Authentification requise
- Vérification des permissions
- Rate limiting
- Validation des préférences
- Protection CSRF
- Sécurisation des canaux de diffusion
- Sanitization des données de notification 
