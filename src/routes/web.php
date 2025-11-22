<?php

use Illuminate\Support\Facades\Route;
use Slsabil\NotificationCenter\Http\Controllers\NotificationController;

Route::middleware(['web', 'auth'])
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function () {

        Route::get('/menu', [NotificationController::class, 'menu'])->name('menu');
        Route::post('/{recipient}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
        Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');

        // صفحة index افتراضية بسيطة (اختياري)
        Route::get('/', [NotificationController::class, 'index'])->name('index');
    });
