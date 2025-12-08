<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\Transaction;
use App\Models\Event;
class TransactionService
{
    public function createTransaction($eventId, $userId, $qty, $promoCode = null)
    {
        $subtotal = DB::table('events')->where('id', $eventId)->value('price') * $qty;
        $discount = 0;
        $promoId = null;

        // Handle promo
        if ($promoCode) {
            $promo = $this->validatePromoInternally($promoCode, $subtotal);

            if ($promo['success']) {
                $discount = $promo['discount_amount'];
                $promoId = $promo['promo_id'];
            }
        }

        $finalTotal = $subtotal - $discount;

        // CALL Stored Procedure
        DB::statement('CALL SP_CreateTransaction(?, ?, ?, @trx_id, @msg)', [
            $eventId,
            $userId,
            $qty
        ]);

        $result = DB::select('SELECT @trx_id AS transaction_id, @msg AS message')[0];

        if ($result->message === 'Success' && $result->transaction_id) {

            // Update with promo if applied
            if ($promoId) {
                DB::table('transactions')
                    ->where('id', $result->transaction_id)
                    ->update([
                        'promo_code_id' => $promoId,
                        'discount_amount' => $discount,
                        'total_amount' => $finalTotal,
                    ]);

                DB::table('promo_codes')->where('id', $promoId)->increment('uses');
            }

            return [
                'success' => true,
                'transaction' => Transaction::find($result->transaction_id)
            ];
        }

        return [
            'success' => false,
            'message' => $result->message
        ];
    }
     public function getStats()
    {
        return [
            'total_events' => Event::count(),
            'total_transactions' => Transaction::count(),
            'total_revenue' => Transaction::where('status', 'paid')->sum('total_amount'),
            'pending_transactions' => Transaction::where('status', 'pending')->count(),
        ];
    }

    /**
     * 🎯 Validate promo for AJAX request
     */
    public function validatePromo($promoCode, $subtotal)
    {
        try {
            $promo = DB::table('promo_codes')->where('code', $promoCode)->first();

            if (!$promo) return $this->response(false, 'Promo code not found', $subtotal);

            if (!$promo->active) return $this->response(false, 'Promo inactive', $subtotal);

            if (now()->lt($promo->valid_from)) return $this->response(false, 'Promo not yet valid', $subtotal);

            if (now()->gt($promo->valid_until)) return $this->response(false, 'Promo expired', $subtotal);

            if ($promo->uses >= $promo->max_uses) return $this->response(false, 'Promo usage limit reached', $subtotal);

            $userUsed = DB::table('transactions')
                ->where('user_id', Auth::id())
                ->where('promo_code_id', $promo->id)
                ->whereIn('status', ['paid', 'pending'])
                ->count();

            if ($userUsed > 0) return $this->response(false, 'Already used this promo', $subtotal);

            // Calculate discount
            $discount = $promo->type === 'flat'
                ? $promo->value
                : ($subtotal * $promo->value) / 100;

            $discount = min($discount, $subtotal);
            $finalAmount = $subtotal - $discount;

            return [
                'success' => true,
                'message' => 'Promo applied successfully',
                'discount_amount' => $discount,
                'final_amount' => $finalAmount,
                'promo_id' => $promo->id,
            ];

        } catch (\Exception $e) {
            return $this->response(false, "Validation error: {$e->getMessage()}", $subtotal);
        }
    }

    /**
     * Internal reuse (avoid duplicate logic)
     */
    private function validatePromoInternally($promoCode, $subtotal)
    {
        return $this->validatePromo($promoCode, $subtotal);
    }

    private function response($success, $message, $subtotal)
    {
        return [
            'success' => $success,
            'message' => $message,
            'discount_amount' => 0,
            'final_amount' => $subtotal
        ];
    }


    public function getTransactionById($id)
    {
        return Transaction::findOrFail($id);
    }

    public function getUserTransactions($userId)
    {
        return Transaction::where('user_id', $userId)->latest()->get();
    }
}
