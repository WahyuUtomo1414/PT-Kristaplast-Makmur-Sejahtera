<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderExportController;

Route::get('/', function () {
    return redirect("/orders/login");
});

Route::get('/orders/{order}/export-pdf', [OrderExportController::class, 'export'])
    ->name('orders.export.pdf');