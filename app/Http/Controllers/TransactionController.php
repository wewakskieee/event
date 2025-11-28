<?php
// app/Http/Controllers/TransactionController.php

namespace App\Http\Controllers;

use App\Services\TransactionService;
use App\Services\EventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Transaction;

class TransactionController extends Controller
{
    protected $transactionService;
    protected $eventService;

    public function __construct(
        TransactionService $transactionService,
        EventService $eventService
    ) {
        $this->transactionService = $transactionService;
        $this->eventService = $eventService;
    }

    public function checkout(Request $request)
    {
        $event = $this->eventService->getEventById($request->event_id);
        
        return view('checkout.index', compact('event'));
    }

    public function processCheckout(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $result = $this->transactionService->createTransaction(
            $validated['event_id'],
            Auth::id(),
            $validated['quantity']
        );

        if ($result['success']) {
            return redirect()->route('invoice.show', $result['transaction']->id)
                ->with('success', 'Transaction created successfully');
        }

        return back()->with('error', $result['message']);
    }

    public function invoice($id)
    {
        $transaction = $this->transactionService->getTransactionById($id);
        
        // Check authorization
        if ($transaction->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        return view('invoice.show', compact('transaction'));
    }

    public function myTransactions()
    {
        $transactions = $this->transactionService->getUserTransactions(Auth::id());
        
        return view('transactions.index', compact('transactions'));
    }

    // ADMIN METHODS
    public function adminIndex(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Transaction::with(['user', 'items.event']);
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $transactions = $query->latest()->paginate(20);
        
        $stats = [
            'total' => Transaction::count(),
            'pending' => Transaction::where('status', 'pending')->count(),
            'paid' => Transaction::where('status', 'paid')->count(),
            'canceled' => Transaction::where('status', 'canceled')->count(),
        ];
        
        return view('admin.transactions.index', compact('transactions', 'stats'));
    }
    public function showTickets($id)
{
    $transaction = Transaction::with(['user', 'items.event', 'tickets.event'])
        ->findOrFail($id);
    
    return view('admin.transactions.tickets', compact('transaction'));
}
    public function updateStatus(Request $request, $id)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,paid,canceled',
        ]);

        $transaction = Transaction::with('items')->findOrFail($id);
        
        // Jika status berubah ke canceled, kembalikan quota
        if ($validated['status'] === 'canceled' && $transaction->status !== 'canceled') {
            DB::transaction(function () use ($transaction) {
                foreach ($transaction->items as $item) {
                    DB::table('events')
                        ->where('id', $item->event_id)
                        ->increment('quota_remaining', $item->quantity);
                }
            });
        }

        // Update status
        $updateData = ['status' => $validated['status']];
        
        if ($validated['status'] === 'paid') {
            $updateData['paid_at'] = now();
        }
        
        $transaction->update($updateData);

        return back()->with('success', 'Transaction status updated to ' . $validated['status']);
    }
}
