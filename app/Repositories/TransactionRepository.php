<?php
// app/Repositories/TransactionRepository.php

namespace App\Repositories;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    protected $model;

    public function __construct(Transaction $transaction)
    {
        $this->model = $transaction;
    }

    public function all()
    {
        return $this->model->with(['user', 'items.event'])->latest()->get();
    }

    public function find($id)
    {
        return $this->model->with(['user', 'items.event', 'tickets'])->findOrFail($id);
    }

    public function findByCode($code)
    {
        return $this->model->with(['user', 'items.event', 'tickets'])
            ->where('transaction_code', $code)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $transaction = $this->find($id);
        $transaction->update($data);
        return $transaction;
    }

    public function getUserTransactions($userId)
    {
        return $this->model
            ->with(['items.event', 'tickets'])
            ->where('user_id', $userId)
            ->latest()
            ->get();
    }

    public function createWithStoredProcedure($eventId, $userId, $quantity)
    {
        $transactionId = 0;
        $message = '';

        DB::statement('CALL SP_CreateTransaction(?, ?, ?, @transaction_id, @message)', [
            $eventId,
            $userId,
            $quantity
        ]);

        $result = DB::select('SELECT @transaction_id as transaction_id, @message as message');

        return [
            'transaction_id' => $result[0]->transaction_id,
            'message' => $result[0]->message
        ];
    }
}
