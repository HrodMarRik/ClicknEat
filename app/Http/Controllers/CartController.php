<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Dish;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cart = null;

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())
                        ->with(['items.dish', 'restaurant'])
                        ->first();
        } else {
            if (session()->has('cart')) {
                $cartData = session('cart');
                $cart = new Cart();
                $cart->restaurant_id = $cartData['restaurant_id'];
                $cart->subtotal = $cartData['subtotal'];
                $cart->delivery_fee = $cartData['delivery_fee'];
                $cart->total = $cartData['total'];

                $cart->restaurant = Restaurant::find($cartData['restaurant_id']);
                $cart->items = collect();

                foreach ($cartData['items'] as $item) {
                    $cartItem = new \App\Models\CartItem();
                    $cartItem->dish_id = $item['dish_id'];
                    $cartItem->quantity = $item['quantity'];
                    $cartItem->price = $item['price'];
                    $cartItem->dish = Dish::find($item['dish_id']);

                    $cart->items->push($cartItem);
                }
            }
        }

        return view('cart.index', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'dish_id' => 'required|exists:dishes,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $dish = Dish::findOrFail($request->dish_id);

        if (!$dish->is_available) {
            return redirect()->back()->with('error', 'Ce plat n\'est plus disponible.');
        }

        if (Auth::check()) {
            $cart = Cart::firstOrNew(['user_id' => Auth::id()]);

            if ($cart->exists && $cart->restaurant_id != $dish->restaurant_id) {
                return redirect()->back()->with('error', 'Votre panier contient des plats d\'un autre restaurant. Videz votre panier avant d\'ajouter des plats d\'un nouveau restaurant.');
            }

            if (!$cart->exists) {
                $cart->restaurant_id = $dish->restaurant_id;
                $cart->subtotal = 0;
                $cart->delivery_fee = $dish->restaurant->delivery_fee;
                $cart->total = 0;
                $cart->save();
            }

            $cartItem = $cart->items()->where('dish_id', $dish->id)->first();

            if ($cartItem) {
                $cartItem->quantity += $request->quantity;
                $cartItem->save();
            } else {
                $cart->items()->create([
                    'dish_id' => $dish->id,
                    'quantity' => $request->quantity,
                    'price' => $dish->price
                ]);
            }

            $this->updateCartTotals($cart);

        } else {
            $cartData = session()->get('cart', [
                'restaurant_id' => $dish->restaurant_id,
                'items' => [],
                'subtotal' => 0,
                'delivery_fee' => $dish->restaurant->delivery_fee,
                'total' => 0
            ]);

            if ($cartData['restaurant_id'] != $dish->restaurant_id) {
                return redirect()->back()->with('error', 'Votre panier contient des plats d\'un autre restaurant. Videz votre panier avant d\'ajouter des plats d\'un nouveau restaurant.');
            }

            $itemExists = false;

            foreach ($cartData['items'] as &$item) {
                if ($item['dish_id'] == $dish->id) {
                    $item['quantity'] += $request->quantity;
                    $itemExists = true;
                    break;
                }
            }

            if (!$itemExists) {
                $cartData['items'][] = [
                    'dish_id' => $dish->id,
                    'quantity' => $request->quantity,
                    'price' => $dish->price
                ];
            }

            $this->updateSessionCartTotals($cartData);

            session()->put('cart', $cartData);
        }

        return redirect()->back()->with('success', 'Le plat a été ajouté à votre panier.');
    }

    public function update(Request $request)
    {
        $request->validate([
            'dish_id' => 'required|exists:dishes,id',
            'action' => 'required|in:increase,decrease',
        ]);

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->firstOrFail();
            $cartItem = $cart->items()->where('dish_id', $request->dish_id)->firstOrFail();

            if ($request->action == 'increase') {
                $cartItem->quantity += 1;
            } else {
                $cartItem->quantity -= 1;
            }

            if ($cartItem->quantity <= 0) {
                $cartItem->delete();
            } else {
                $cartItem->save();
            }

            $this->updateCartTotals($cart);

            if ($cart->items()->count() == 0) {
                $cart->delete();
                return redirect()->route('restaurants.index')->with('success', 'Votre panier est maintenant vide.');
            }

        } else {
            if (!session()->has('cart')) {
                return redirect()->route('restaurants.index');
            }

            $cartData = session('cart');

            foreach ($cartData['items'] as $key => &$item) {
                if ($item['dish_id'] == $request->dish_id) {
                    if ($request->action == 'increase') {
                        $item['quantity'] += 1;
                    } else {
                        $item['quantity'] -= 1;
                    }

                    if ($item['quantity'] <= 0) {
                        unset($cartData['items'][$key]);
                        $cartData['items'] = array_values($cartData['items']);
                    }

                    break;
                }
            }

            $this->updateSessionCartTotals($cartData);

            if (count($cartData['items']) == 0) {
                session()->forget('cart');
                return redirect()->route('restaurants.index')->with('success', 'Votre panier est maintenant vide.');
            }

            session()->put('cart', $cartData);
        }

        return redirect()->back()->with('success', 'Panier mis à jour.');
    }

    public function remove(Request $request)
    {
        $request->validate([
            'dish_id' => 'required|exists:dishes,id',
        ]);

        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->firstOrFail();
            $cart->items()->where('dish_id', $request->dish_id)->delete();

            $this->updateCartTotals($cart);

            if ($cart->items()->count() == 0) {
                $cart->delete();
                return redirect()->route('restaurants.index')->with('success', 'Votre panier est maintenant vide.');
            }

        } else {
            if (!session()->has('cart')) {
                return redirect()->route('restaurants.index');
            }

            $cartData = session('cart');

            foreach ($cartData['items'] as $key => $item) {
                if ($item['dish_id'] == $request->dish_id) {
                    unset($cartData['items'][$key]);
                    $cartData['items'] = array_values($cartData['items']);
                    break;
                }
            }

            $this->updateSessionCartTotals($cartData);

            if (count($cartData['items']) == 0) {
                session()->forget('cart');
                return redirect()->route('restaurants.index')->with('success', 'Votre panier est maintenant vide.');
            }

            session()->put('cart', $cartData);
        }

        return redirect()->back()->with('success', 'Plat retiré du panier.');
    }

    public function clear()
    {
        if (Auth::check()) {
            $cart = Cart::where('user_id', Auth::id())->first();

            if ($cart) {
                $cart->items()->delete();
                $cart->delete();
            }
        } else {
            session()->forget('cart');
        }

        return redirect()->route('restaurants.index')->with('success', 'Votre panier a été vidé.');
    }

    private function updateCartTotals(Cart $cart)
    {
        $subtotal = 0;

        foreach ($cart->items as $item) {
            $subtotal += $item->price * $item->quantity;
        }

        $cart->subtotal = $subtotal;
        $cart->total = $subtotal + $cart->delivery_fee;
        $cart->save();
    }

    private function updateSessionCartTotals(&$cartData)
    {
        $subtotal = 0;

        foreach ($cartData['items'] as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $cartData['subtotal'] = $subtotal;
        $cartData['total'] = $subtotal + $cartData['delivery_fee'];
    }
}
