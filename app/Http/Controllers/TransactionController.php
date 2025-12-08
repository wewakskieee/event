<?php

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
            'promo_code' => 'nullable|string'
        ]);

        $result = $this->transactionService->createTransaction(
            $validated['event_id'],
            Auth::id(),
            $validated['quantity'],
            $validated['promo_code'] ?? null
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


    /**
     * 🔥 Apply Promo Code AJAX Handler
     */
    public function applyPromo(Request $request)
    {
        $data = $request->validate([
            'event_id' => 'required|integer|exists:transactions,event_id',
            'promo_code' => 'required|string',
        ]);

        // Cari transaksi pending user untuk event ini
        $transaction = Transaction::where('event_id', $data['event_id'])
            ->where('user_id', Auth::id())
            ->where('status', 'pending')
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Transaction not found or already processed',
            ], 404);
        }

        // Call Stored Procedure
        DB::statement('CALL SP_ApplyPromoCode(?, ?, ?, @discount, @final, @msg)', [
            $transaction->id,
            $data['promo_code'],
            Auth::id(),
        ]);

        $result = DB::select('SELECT @discount AS discount_amount, @final AS final_amount, @msg AS message')[0];

        $success = ($result->message === 'Promo code applied successfully');

        return response()->json([
            'success' => $success,
            'discount_amount' => (float) $result->discount_amount,
            'final_amount' => (float) $result->final_amount,
            'message' => $result->message,
        ]);
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
    // app/Http/Controllers/TransactionController.php

public function validatePromo(Request $request)
{
    $data = $request->validate([
        'promo_code' => 'required|string',
        'subtotal' => 'required|numeric|min:0',
    ]);

    try {
        $promo = DB::table('promo_codes')
            ->where('code', $data['promo_code'])
            ->first();

        // Validasi promo
        if (!$promo) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not found',
                'discount_amount' => 0,
                'final_amount' => $data['subtotal']
            ]);
        }

        if (!$promo->active) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code is inactive',
                'discount_amount' => 0,
                'final_amount' => $data['subtotal']
            ]);
        }

        if (now()->lt($promo->valid_from)) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code not yet valid',
                'discount_amount' => 0,
                'final_amount' => $data['subtotal']
            ]);
        }

        if (now()->gt($promo->valid_until)) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code has expired',
                'discount_amount' => 0,
                'final_amount' => $data['subtotal']
            ]);
        }

        if ($promo->uses >= $promo->max_uses) {
            return response()->json([
                'success' => false,
                'message' => 'Promo code usage limit reached',
                'discount_amount' => 0,
                'final_amount' => $data['subtotal']
            ]);
        }

        // Cek apakah user sudah pernah pakai promo ini
        $userUsage = DB::table('transactions')
            ->where('user_id', auth()->id())
            ->where('promo_code_id', $promo->id)
            ->whereIn('status', ['paid', 'pending'])
            ->count();

        if ($userUsage > 0) {
            return response()->json([
                'success' => false,
                'message' => 'You have already used this promo code',
                'discount_amount' => 0,
                'final_amount' => $data['subtotal']
            ]);
        }

        // Hitung diskon
        $discount = 0;
        if ($promo->type === 'flat') {
            $discount = $promo->value;
        } elseif ($promo->type === 'percent') {
            $discount = ($data['subtotal'] * $promo->value) / 100;
        }

        // Pastikan diskon tidak melebihi subtotal
        if ($discount > $data['subtotal']) {
            $discount = $data['subtotal'];
        }

        $finalAmount = $data['subtotal'] - $discount;

        return response()->json([
            'success' => true,
            'message' => 'Promo code applied successfully',
            'discount_amount' => $discount,
            'final_amount' => $finalAmount,
            'promo_id' => $promo->id
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to validate promo code: ' . $e->getMessage(),
            'discount_amount' => 0,
            'final_amount' => $data['subtotal']
        ], 500);
    }
}

    public function updateStatus(Request $request, $id)
{
    $validated = $request->validate([
        'status' => 'required|in:pending,paid,canceled',
    ]);

    $transaction = Transaction::findOrFail($id);
    
    // ✅ HANYA UPDATE STATUS - Biar trigger yang handle restore quota
    $updateData = ['status' => $validated['status']];
    
    if ($validated['status'] === 'paid') {
        $updateData['paid_at'] = now();
    }
    
    $transaction->update($updateData);

    return back()->with('success', 'Transaction status updated to ' . $validated['status']);
}

}
