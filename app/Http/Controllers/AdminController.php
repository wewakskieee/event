<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Transaction;
use App\Services\EventService;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    protected $eventService;
    protected $transactionService;

    public function __construct(
        EventService $eventService,
        TransactionService $transactionService
    ) {
        $this->eventService = $eventService;
        $this->transactionService = $transactionService;
    }

    public function dashboard()
    {
        // Stats dari Service
        $eventStats = $this->eventService->getStats();
        $transactionStats = $this->transactionService->getStats();

        // Custom Stats tambahan
        $totalEvents = Event::count();
        $totalTransactions = Transaction::count();
        $totalRevenue = Transaction::where('status', 'paid')->sum('total_amount');
        $pendingTransactions = Transaction::where('status', 'pending')->count();

        // Events dengan total tiket terjual via FUNCTION
        $events = Event::select('id', 'title', 'quota', 'quota_remaining')
            ->selectRaw('f_total_ticket_sold(id) as tickets_sold')
            ->limit(10)
            ->get();

        // Gabung data menjadi array agar mudah dipakai di view
        $stats = array_merge($eventStats, $transactionStats, [
            'totalEvents' => $totalEvents,
            'totalTransactions' => $totalTransactions,
            'totalRevenue' => $totalRevenue,
            'pendingTransactions' => $pendingTransactions
        ]);

        return view('admin.dashboard', compact('stats', 'events'));
    }
}
