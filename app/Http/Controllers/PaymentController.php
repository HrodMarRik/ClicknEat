<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Stripe\Exception\CardException;
use Stripe\StripeClient;

class PaymentController extends Controller
{
    protected $stripe;

    public function __construct()
    {
        $this->middleware('auth');
        $this->stripe = new StripeClient(config('cashier.secret'));
    }

    public function checkout(Order $order)
    {
        $this->authorize('view', $order);

        if ($order->is_paid) {
            return redirect()->route('orders.show', $order)
                ->with('info', 'Cette commande a déjà été payée.');
        }

        // Validation automatique du paiement sans passer par Stripe
        $order->update([
            'is_paid' => true,
            'status' => 'confirmed',
            'payment_intent_id' => 'AUTO-' . uniqid(), // Identifiant fictif pour le paiement automatique
        ]);

        // Générer la facture
        $user = $order->user;
        if (!$user->hasStripeId()) {
            $user->createAsStripeCustomer();
        }

        $invoice = $user->invoiceFor(
            'Commande #' . $order->id,
            $order->total_price * 100
        );

        return redirect()->route('orders.show', $order)
            ->with('success', 'Paiement validé automatiquement avec succès!');
    }

    public function process(Request $request, Order $order)
    {
        $this->authorize('view', $order);

        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($order->payment_intent_id);

            if ($paymentIntent->status === 'succeeded') {
                $order->update([
                    'is_paid' => true,
                    'status' => 'confirmed',
                ]);

                // Generate invoice
                $user = $order->user;
                if (!$user->hasStripeId()) {
                    $user->createAsStripeCustomer();
                }

                $invoice = $user->invoiceFor(
                    'Commande #' . $order->id,
                    $order->total_price * 100
                );

                return redirect()->route('orders.show', $order)
                    ->with('success', 'Paiement effectué avec succès!');
            } else {
                return back()->with('error', 'Le paiement n\'a pas été complété.');
            }
        } catch (CardException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    public function webhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = config('cashier.webhook.secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );

            if ($event->type === 'payment_intent.succeeded') {
                $paymentIntent = $event->data->object;
                $order = Order::where('payment_intent_id', $paymentIntent->id)->first();

                if ($order && !$order->is_paid) {
                    $order->update([
                        'is_paid' => true,
                        'status' => 'confirmed',
                    ]);
                }
            }

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
