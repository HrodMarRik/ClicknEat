<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;

// Stripe Webhook
Route::post(
    'stripe/webhook',
    [PaymentController::class, 'webhook']
)->name('cashier.webhook');
