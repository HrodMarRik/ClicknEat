# Transaction Model

`App\Models\Transaction`

Ce modèle représente les transactions financières liées aux commandes et aux paiements.

## Structure de la Table

```php
Schema::create('transactions', function (Blueprint $table) {
    $table->id();
    $table->uuid('reference')->unique();
    $table->foreignId('order_id')->constrained();
    $table->foreignId('user_id')->constrained();
    $table->foreignId('payment_method_id')->nullable()->constrained();
    $table->string('type');
    $table->string('status');
    $table->decimal('amount', 10, 2);
    $table->string('currency', 3);
    $table->string('provider');
    $table->string('provider_transaction_id')->nullable();
    $table->json('provider_response')->nullable();
    $table->text('description')->nullable();
    $table->text('error_message')->nullable();
    $table->timestamp('processed_at')->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## Relations

```php
class Transaction extends Model
{
    // Appartient à une commande
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Appartient à un utilisateur
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Appartient à une méthode de paiement
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    // A plusieurs remboursements
    public function refunds()
    {
        return $this->hasMany(Transaction::class, 'parent_transaction_id')
            ->where('type', self::TYPES['REFUND']);
    }
}
```

## Attributs

```php
protected $fillable = [
    'reference',
    'order_id',
    'user_id',
    'payment_method_id',
    'type',
    'status',
    'amount',
    'currency',
    'provider',
    'provider_transaction_id',
    'provider_response',
    'description',
    'error_message'
];

protected $casts = [
    'amount' => 'decimal:2',
    'provider_response' => 'array',
    'processed_at' => 'datetime'
];

// Types de transaction
const TYPES = [
    'PAYMENT' => 'payment',
    'REFUND' => 'refund',
    'CHARGEBACK' => 'chargeback'
];

// Statuts de transaction
const STATUSES = [
    'PENDING' => 'pending',
    'PROCESSING' => 'processing',
    'COMPLETED' => 'completed',
    'FAILED' => 'failed',
    'CANCELLED' => 'cancelled'
];

// Fournisseurs de paiement
const PROVIDERS = [
    'STRIPE' => 'stripe',
    'PAYPAL' => 'paypal',
    'ADYEN' => 'adyen'
];
```

## Scopes

```php
// Transactions réussies
public function scopeSuccessful($query)
{
    return $query->where('status', self::STATUSES['COMPLETED']);
}

// Transactions par type
public function scopeOfType($query, $type)
{
    return $query->where('type', $type);
}

// Transactions par fournisseur
public function scopeByProvider($query, $provider)
{
    return $query->where('provider', $provider);
}

// Transactions récentes
public function scopeRecent($query)
{
    return $query->orderBy('created_at', 'desc');
}
```

## Méthodes

```php
// Traite la transaction
public function process()
{
    try {
        $this->update(['status' => self::STATUSES['PROCESSING']]);

        $paymentProvider = app(PaymentProviderFactory::class)
            ->create($this->provider);

        $result = $paymentProvider->processTransaction($this);

        if ($result->isSuccessful()) {
            $this->markAsCompleted($result->getData());
            event(new TransactionCompleted($this));
            return true;
        }

        $this->markAsFailed($result->getError());
        return false;

    } catch (\Exception $e) {
        $this->markAsFailed($e->getMessage());
        report($e);
        return false;
    }
}

// Marque la transaction comme complétée
public function markAsCompleted(array $providerData)
{
    $this->update([
        'status' => self::STATUSES['COMPLETED'],
        'provider_response' => $providerData,
        'provider_transaction_id' => $providerData['transaction_id'] ?? null,
        'processed_at' => now()
    ]);
}

// Marque la transaction comme échouée
public function markAsFailed(string $errorMessage)
{
    $this->update([
        'status' => self::STATUSES['FAILED'],
        'error_message' => $errorMessage,
        'processed_at' => now()
    ]);

    event(new TransactionFailed($this));
}

// Crée un remboursement
public function createRefund(float $amount, string $reason): Transaction
{
    if ($amount > $this->amount) {
        throw new InvalidAmountException('Refund amount exceeds transaction amount');
    }

    return DB::transaction(function () use ($amount, $reason) {
        $refund = $this->refunds()->create([
            'reference' => Str::uuid(),
            'order_id' => $this->order_id,
            'user_id' => $this->user_id,
            'type' => self::TYPES['REFUND'],
            'status' => self::STATUSES['PENDING'],
            'amount' => $amount,
            'currency' => $this->currency,
            'provider' => $this->provider,
            'description' => $reason
        ]);

        $refund->process();
        return $refund;
    });
}
```

## Events

```php
protected $dispatchesEvents = [
    'created' => TransactionCreated::class,
    'updated' => TransactionUpdated::class
];
```

## Observers

```php
class TransactionObserver
{
    public function creating(Transaction $transaction)
    {
        if (empty($transaction->reference)) {
            $transaction->reference = Str::uuid();
        }
    }

    public function created(Transaction $transaction)
    {
        if ($transaction->status === self::STATUSES['PENDING']) {
            ProcessTransaction::dispatch($transaction)
                ->delay(now()->addSeconds(5));
        }
    }
}
```

## Validation

```php
class TransactionValidator
{
    public static function rules()
    {
        return [
            'type' => ['required', Rule::in(Transaction::TYPES)],
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'provider' => ['required', Rule::in(Transaction::PROVIDERS)],
            'description' => 'nullable|string|max:500'
        ];
    }
}
```

## Notes de Sécurité

- Validation des montants
- Protection des données de paiement
- Vérification des permissions
- Logging des transactions
- Gestion sécurisée des remboursements
- Validation des devises
- Protection contre les doubles paiements
- Conformité PCI DSS 
