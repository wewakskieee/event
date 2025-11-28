<?php
// app/Services/TransactionService.php

namespace App\Services;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    protected $transactionRepository;
    protected $eventRepository;

    public function __construct(
        TransactionRepositoryInterface $transactionRepository,
        EventRepositoryInterface $eventRepository
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->eventRepository = $eventRepository;
    }

    public function getAllTransactions()
    {
        return $this->transactionRepository->all();
    }

    public function getTransactionById($id)
    {
        return $this->transactionRepository->find($id);
    }

    public function getUserTransactions($userId)
    {
        return $this->transactionRepository->getUserTransactions($userId);
    }

    public function createTransaction($eventId, $userId, $quantity)
    {
        $result = $this->transactionRepository->createWithStoredProcedure(
            $eventId,
            $userId,
            $quantity
        );

        if ($result['transaction_id'] > 0) {
            return [
                'success' => true,
                'transaction' => $this->transactionRepository->find($result['transaction_id']),
                'message' => $result['message']
            ];
        }

        return [
            'success' => false,
            'transaction' => null,
            'message' => $result['message']
        ];
    }

    public function updatePaymentStatus($id, $status)
    {
        $data = ['status' => $status];
        
        if ($status === 'paid') {
            $data['paid_at'] = now();
        }

        return $this->transactionRepository->update($id, $data);
    }

    public function cancelTransaction($id)
    {
        $transaction = $this->transactionRepository->find($id);

        if ($transaction->status !== 'pending') {
            throw new \Exception('Only pending transactions can be canceled');
        }

        DB::transaction(function () use ($transaction, $id) {
            // Restore quota
            foreach ($transaction->items as $item) {
                DB::table('events')
                    ->where('id', $item->event_id)
                    ->increment('quota_remaining', $item->quantity);
            }

            // Update status
            $this->transactionRepository->update($id, ['status' => 'canceled']);
        });

        return $transaction;
    }

    public function getStats()
    {
        $transactions = $this->transactionRepository->all();
        
        return [
            'total_transactions' => $transactions->count(),
            'total_revenue' => $transactions->where('status', 'paid')->sum('total_amount'),
            'pending_transactions' => $transactions->where('status', 'pending')->count(),
            'paid_transactions' => $transactions->where('status', 'paid')->count(),
        ];
    }
}
