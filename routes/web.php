<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EventController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LogoutController;

// Public Routes
Route::get('/', [EventController::class, 'index'])->name('home');
Route::get('/event/{slug}', [EventController::class, 'show'])->name('event.detail');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);
});

Route::post('/logout', [LogoutController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated User Routes
Route::middleware(['auth', 'auth.user'])->group(function () {
    Route::get('/checkout', [TransactionController::class, 'checkout'])->name('checkout');
    Route::post('/checkout', [TransactionController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/invoice/{id}', [TransactionController::class, 'invoice'])->name('invoice.show');
    Route::get('/my-transactions', [TransactionController::class, 'myTransactions'])->name('transactions.my');
});

// Ticket Scanner Routes
Route::prefix('ticket')->group(function () {
    Route::get('/scan', [TicketController::class, 'scanPage'])->name('ticket.scan');
    Route::post('/validate', [TicketController::class, 'validate'])->name('ticket.validate');
    Route::post('/use', [TicketController::class, 'use'])->name('ticket.use');
    Route::get('/qr/{ticketCode}', [TicketController::class, 'generateQr'])->name('ticket.qr');
});

// Admin Routes
Route::middleware(['auth', 'auth.admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Events Management
    Route::get('/events', [EventController::class, 'adminIndex'])->name('events.index');
    Route::get('/events/create', [EventController::class, 'create'])->name('events.create');
    Route::post('/events', [EventController::class, 'store'])->name('events.store');
    Route::get('/events/{id}/edit', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/events/{id}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/events/{id}', [EventController::class, 'destroy'])->name('events.destroy');
    
    // Banners Management
    Route::get('/banners', [BannerController::class, 'index'])->name('banners.index');
    Route::get('/banners/create', [BannerController::class, 'create'])->name('banners.create');
    Route::post('/banners', [BannerController::class, 'store'])->name('banners.store');
    Route::delete('/banners/{id}', [BannerController::class, 'destroy'])->name('banners.destroy');
    
    // Transaction Management
    Route::get('/transactions', [TransactionController::class, 'adminIndex'])->name('transactions.index');
    Route::post('/transactions/{id}/update-status', [TransactionController::class, 'updateStatus'])->name('transactions.update-status');
    Route::get('/transactions/{id}/tickets', [TransactionController::class, 'showTickets'])->name('transactions.tickets');
});
