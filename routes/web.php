<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RestaurantController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RestaurantController as AdminRestaurantController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Restaurateur\DashboardController as RestaurateurDashboardController;
use App\Http\Controllers\Restaurateur\RestaurantController as RestaurateurRestaurantController;
use App\Http\Controllers\Restaurateur\DishController;
use App\Http\Controllers\Restaurateur\CategoryController;
use App\Http\Controllers\Restaurateur\OrderController as RestaurateurOrderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Routes publiques
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/restaurants', [RestaurantController::class, 'index'])->name('restaurants.index');
Route::get('/restaurants/{restaurant}', [RestaurantController::class, 'show'])->name('restaurants.show');

// Routes pour les utilisateurs authentifiés
Route::middleware('auth')->group(function () {
    // Tableau de bord utilisateur
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Panier
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/remove', [CartController::class, 'remove'])->name('cart.remove');
    Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

    // Commandes
    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

    // Avis
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Paiement
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');
});

// Routes pour les administrateurs
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Gestion des utilisateurs
    Route::resource('users', UserController::class);
    Route::post('/users/{user}/activate', [UserController::class, 'activate'])->name('users.activate');
    Route::post('/users/{user}/deactivate', [UserController::class, 'deactivate'])->name('users.deactivate');

    // Gestion des restaurants
    Route::resource('restaurants', RestaurantController::class);

    // Gestion des commandes
    Route::resource('orders', OrderController::class);

    // Routes pour les catégories
    Route::resource('categories', CategoryController::class)->except(['index', 'show']);

    // Routes pour les plats
    Route::resource('dishes', DishController::class)->except(['index', 'show']);

    // Nouvelle route pour mettre à jour rapidement le statut d'une commande
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
});

// Routes pour les restaurateurs
Route::middleware(['auth', 'role:restaurateur'])->prefix('restaurateur')->name('restaurateur.')->group(function () {
    Route::get('/dashboard', [RestaurateurDashboardController::class, 'index'])->name('dashboard');

    // Gestion du restaurant
    Route::resource('restaurant', RestaurateurRestaurantController::class)->only(['index', 'create', 'store', 'edit', 'update']);

    // Gestion des catégories
    Route::resource('categories', CategoryController::class);

    // Gestion des plats
    Route::resource('dishes', DishController::class);

    // Gestion des commandes
    Route::resource('orders', RestaurateurOrderController::class)->only(['index', 'show', 'update']);
});

require __DIR__.'/auth.php';
