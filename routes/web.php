<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

// Public Routes
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/event/{slug}', [EventController::class, 'show'])->name('event.detail');

// Authentication
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LogoutController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// User Routes
Route::middleware(['auth', 'auth.user'])->group(function () {
    Route::get('/checkout', [TransactionController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [TransactionController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/invoice/{id}', [TransactionController::class, 'invoice'])->name('invoice.show');
    Route::get('/my-transactions', [TransactionController::class, 'myTransactions'])->name('transactions.my');
    Route::post('/promo/validate', [TransactionController::class, 'validatePromo'])->name('promo.validate');
});

// Ticket Routes
Route::prefix('ticket')->group(function () {
    Route::get('/scan', [TicketController::class, 'scanPage'])->name('ticket.scan');
    Route::post('/validate', [TicketController::class, 'validate'])->name('ticket.validate');
    Route::post('/use', [TicketController::class, 'use'])->name('ticket.use');
    Route::get('/qr/{ticketCode}', [TicketController::class, 'generateQr'])->name('ticket.qr');
});

// ADMIN ROUTES
Route::middleware(['auth', 'auth.admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Reports Group
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales-detail', [ReportController::class, 'salesDetail'])->name('sales-detail');
        Route::get('/event-analytics', [ReportController::class, 'eventAnalytics'])->name('event-analytics');
        Route::get('/top-events', [ReportController::class, 'topEvents'])->name('top-events');
    });

    // Events Management
    Route::resource('events', EventController::class)->except(['show']);

    // Banners
    Route::resource('banners', BannerController::class)->only(['index', 'create', 'store', 'destroy']);

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'adminIndex'])->name('transactions.index');
    Route::post('/transactions/{id}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
    Route::get('/transactions/{id}/tickets', [TransactionController::class, 'showTickets'])->name('transactions.tickets');
});
