<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    public function index()
    {
        $cart = Cart::where('user_id', Auth::id())
                    ->with(['items.dish', 'restaurant'])
                    ->first();

        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        return view('checkout.index', compact('cart'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'postal_code' => 'required|string|max:20',
            'phone' => 'required|string|max:20',
            'stripeToken' => 'required',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = Cart::where('user_id', Auth::id())
                    ->with(['items.dish', 'restaurant'])
                    ->first();

        if (!$cart || $cart->items->count() == 0) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        try {
            Stripe::setApiKey(config('services.stripe.secret'));

            $paymentIntent = PaymentIntent::create([
                'amount' => round($cart->total * 100),
                'currency' => 'eur',
                'payment_method_types' => ['card'],
                'description' => 'Commande FoodExpress #' . time(),
                'metadata' => [
                    'user_id' => Auth::id(),
                    'restaurant_id' => $cart->restaurant_id,
                ],
            ]);

            DB::beginTransaction();

            $order = Order::create([
                'user_id' => Auth::id(),
                'restaurant_id' => $cart->restaurant_id,
                'status' => 'pending',
                'subtotal' => $cart->subtotal,
                'delivery_fee' => $cart->delivery_fee,
                'total' => $cart->total,
                'address' => $request->address,
                'city' => $request->city,
                'postal_code' => $request->postal_code,
                'phone' => $request->phone,
                'notes' => $request->notes,
                'payment_intent_id' => $paymentIntent->id,
                'payment_status' => 'pending',
            ]);

            foreach ($cart->items as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'dish_id' => $item->dish_id,
                    'quantity' => $item->quantity,
                    'price' => $item->price,
                    'name' => $item->dish->name,
                ]);
            }

            // Mettre à jour les informations de l'utilisateur si elles ont changé
            $user = Auth::user();
            $user->address = $request->address;
            $user->city = $request->city;
            $user->postal_code = $request->postal_code;
            $user->phone = $request->phone;
            $user->save();

            // Vider le panier
            $cart->items()->delete();
            $cart->delete();

            DB::commit();

            return redirect()->route('client.orders.show', $order)->with('success', 'Votre commande a été passée avec succès !');

        } catch (ApiErrorException $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur de paiement : ' . $e->getMessage());
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Une erreur est survenue lors du traitement de votre commande.');
        }
    }
}
