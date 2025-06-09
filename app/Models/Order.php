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

    // Constantes pour les statuts
    const STATUS_PENDING = 'pending';
    const STATUS_PREPARING = 'preparing';
    const STATUS_READY = 'ready';
    const STATUS_DELIVERING = 'delivering';
    const STATUS_DELIVERED = 'delivered';
    const STATUS_CANCELLED = 'cancelled';

    /**
     * Les statuts possibles pour une commande
     */
    public const STATUS_CONFIRMED = 'confirmed';

    /**
     * Les statuts de paiement possibles
     */
    public const PAYMENT_PENDING = 'pending';
    public const PAYMENT_PAID = 'paid';
    public const PAYMENT_FAILED = 'failed';
    public const PAYMENT_REFUNDED = 'refunded';

    protected $fillable = [
        'user_id',
        'restaurant_id',
        'status',
        'total_price',
        'payment_status',
        'payment_method',
        'pickup_time',
        'special_instructions',
        'table_number'
    ];

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
    public function items()
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

    /**
     * Vérifie si la commande peut être annulée
     */
    public function canBeCancelled()
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED
        ]);
    }

    /**
     * Vérifie si la commande est en cours
     */
    public function isInProgress()
    {
        return in_array($this->status, [
            self::STATUS_PENDING,
            self::STATUS_CONFIRMED,
            self::STATUS_PREPARING,
            self::STATUS_READY,
            self::STATUS_DELIVERING
        ]);
    }

    /**
     * Vérifie si la commande est terminée
     */
    public function isCompleted()
    {
        return $this->status === self::STATUS_DELIVERED;
    }
}
