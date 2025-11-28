<?php
// app/Repositories/Contracts/TransactionRepositoryInterface.php

namespace App\Repositories\Contracts;

interface TransactionRepositoryInterface
{
    public function all();
    public function find($id);
    public function findByCode($code);
    public function create(array $data);
    public function update($id, array $data);
    public function getUserTransactions($userId);
    public function createWithStoredProcedure($eventId, $userId, $quantity);
}
