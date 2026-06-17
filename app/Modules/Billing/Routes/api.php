<?php

use App\Modules\Billing\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('billing/webhooks/{provider}', WebhookController::class)
    ->name('billing.webhooks.handle');
