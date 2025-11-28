<?php
// app/Repositories/Contracts/TicketRepositoryInterface.php

namespace App\Repositories\Contracts;

interface TicketRepositoryInterface
{
    public function all();
    public function find($id);
    public function findByCode($code);
    public function findByQrCode($qrCode);
    public function create(array $data);
    public function update($id, array $data);
    public function validateTicket($qrCode);
    public function markAsUsed($id);
}
