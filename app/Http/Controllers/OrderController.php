<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->authorizeResource(Order::class, 'order');
    }

    /**
     * Affiche la liste des commandes de l'utilisateur
     */
    public function index()
    {
        $user = auth()->user();
        $orders = $user->orders()->with('restaurant')->latest()->paginate(15);

        return view('orders.index', compact('orders'));
    }

    public function create(Request $request, Restaurant $restaurant)
    {
        $restaurant->load('menus.items');
        $cart = session()->get('cart', []);

        return view('orders.create', compact('restaurant', 'cart'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'restaurant_id' => 'required|exists:restaurants,id',
            'items' => 'required|array',
            'items.*.id' => 'required|exists:menu_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
            'pickup_time' => 'required|date|after:now',
            'table_number' => 'nullable|string',
            'payment_method' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $totalPrice = 0;
            $orderItems = [];

            foreach ($validated['items'] as $itemData) {
                $item = \App\Models\MenuItem::findOrFail($itemData['id']);
                $itemTotal = $item->price * $itemData['quantity'];
                $totalPrice += $itemTotal;

                $orderItems[] = [
                    'menu_item_id' => $item->id,
                    'quantity' => $itemData['quantity'],
                    'price' => $item->price,
                    'name' => $item->name,
                    'special_instructions' => $itemData['special_instructions'] ?? null,
                ];
            }

            $order = Order::create([
                'user_id' => auth()->id(),
                'restaurant_id' => $validated['restaurant_id'],
                'total_price' => $totalPrice,
                'special_instructions' => $validated['special_instructions'] ?? null,
                'pickup_time' => $validated['pickup_time'],
                'table_number' => $validated['table_number'] ?? null,
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'payment_status' => 'pending',
            ]);

            $order->items()->createMany($orderItems);

            DB::commit();

            // Clear cart
            session()->forget('cart');

            return redirect()->route('orders.show', $order)
                ->with('success', 'Votre commande a été créée avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Une erreur est survenue: ' . $e->getMessage());
        }
    }

    /**
     * Affiche les détails d'une commande
     */
    public function show(Order $order)
    {
        // Vérifier que l'utilisateur est autorisé à voir cette commande
        $this->authorize('view', $order);

        $order->load(['restaurant', 'items.dish']);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order)
    {
        $order->load(['restaurant', 'items.item']);
        return view('orders.edit', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,confirmed,completed,cancelled',
        ]);

        $order->update($validated);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Statut de la commande mis à jour avec succès!');
    }

    public function destroy(Order $order)
    {
        $order->delete();

        return redirect()->route('orders.index')
            ->with('success', 'Commande supprimée avec succès!');
    }

    public function addToCart(Request $request, Item $item)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'special_instructions' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);

        if (isset($cart[$item->id])) {
            $cart[$item->id]['quantity'] += $validated['quantity'];
        } else {
            $cart[$item->id] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $validated['quantity'],
                'special_instructions' => $validated['special_instructions'] ?? '',
                'restaurant_id' => $item->menu->restaurant_id,
            ];
        }

        session()->put('cart', $cart);

        return back()->with('success', 'Item ajouté au panier!');
    }

    public function cart()
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('restaurants.index')
                ->with('info', 'Votre panier est vide.');
        }

        // Check if all items are from the same restaurant
        $restaurantIds = array_unique(array_column($cart, 'restaurant_id'));

        if (count($restaurantIds) > 1) {
            return back()->with('error', 'Vous ne pouvez commander que d\'un seul restaurant à la fois.');
        }

        $restaurant = Restaurant::find(reset($restaurantIds));

        return view('orders.cart', compact('cart', 'restaurant'));
    }

    public function removeFromCart(Request $request)
    {
        $itemId = $request->input('item_id');
        $cart = session()->get('cart', []);

        if (isset($cart[$itemId])) {
            unset($cart[$itemId]);
            session()->put('cart', $cart);
        }

        return back()->with('success', 'Item retiré du panier!');
    }

    public function clearCart()
    {
        session()->forget('cart');
        return back()->with('success', 'Panier vidé avec succès!');
    }
}
