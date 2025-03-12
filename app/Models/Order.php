<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'status',
        'subtotal',
        'delivery_fee',
        'total',
        'address',
        'city',
        'postal_code',
        'phone',
        'notes',
        'payment_intent_id',
        'payment_status',
        'payment_method',
    ];

    /**
     * Les statuts possibles pour une commande
     */
    public const STATUS_PENDING = 'pending';
    public const STATUS_PREPARING = 'preparing';
    public const STATUS_READY = 'ready';
    public const STATUS_DELIVERING = 'delivering';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';

    /**
     * Les statuts de paiement possibles
     */
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';

    /**
     * Relation avec l'utilisateur qui a passé la commande
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le restaurant qui a reçu la commande
     */
    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    /**
     * Relation avec les éléments de la commande
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relation directe avec les plats via les éléments de commande
     */
    public function dishes()
    {
        return $this->belongsToMany(Dish::class, 'order_items')
            ->withPivot('quantity', 'price')
            ->withTimestamps();
    }

    /**
     * Vérifie si la commande est en attente
     */
    public function isPending()
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Vérifie si la commande est en préparation
     */
    public function isPreparing()
    {
        return $this->status === self::STATUS_PREPARING;
    }

    /**
     * Vérifie si la commande est prête
     */
    public function isReady()
    {
        return $this->status === self::STATUS_READY;
    }

    /**
     * Vérifie si la commande est en cours de livraison
     */
    public function isDelivering()
    {
        return $this->status === self::STATUS_DELIVERING;
    }

    /**
     * Vérifie si la commande est livrée
     */
    public function isDelivered()
    {
        return $this->status === self::STATUS_DELIVERED;
    }

    /**
     * Vérifie si la commande est annulée
     */
    public function isCancelled()
    {
        return $this->status === self::STATUS_CANCELLED;
    }

    /**
     * Vérifie si le paiement est en attente
     */
    public function isPaymentPending()
    {
        return $this->payment_status === self::PAYMENT_PENDING;
    }

    /**
     * Vérifie si le paiement est effectué
     */
    public function isPaymentPaid()
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Vérifie si le paiement a échoué
     */
    public function isPaymentFailed()
    {
        return $this->payment_status === self::PAYMENT_FAILED;
    }

    /**
     * Get the review associated with the order.
     */
    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }
}
