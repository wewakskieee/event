<?php
// app/Repositories/TicketRepository.php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Contracts\TicketRepositoryInterface;

class TicketRepository implements TicketRepositoryInterface
{
    protected $model;

    public function __construct(Ticket $ticket)
    {
        $this->model = $ticket;
    }

    public function all()
    {
        return $this->model->with(['event', 'user', 'transaction'])->latest()->get();
    }

    public function find($id)
    {
        return $this->model->with(['event', 'user', 'transaction'])->findOrFail($id);
    }

    public function findByCode($code)
    {
        return $this->model->with(['event', 'user', 'transaction'])
            ->where('ticket_code', $code)
            ->firstOrFail();
    }

    public function findByQrCode($qrCode)
    {
        return $this->model->with(['event', 'user', 'transaction'])
            ->where('qr_code', $qrCode)
            ->firstOrFail();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $ticket = $this->find($id);
        $ticket->update($data);
        return $ticket;
    }

    public function validateTicket($qrCode)
    {
        $ticket = $this->findByQrCode($qrCode);
        
        if ($ticket->is_used) {
            return [
                'valid' => false,
                'message' => 'Ticket already used',
                'ticket' => $ticket
            ];
        }

        return [
            'valid' => true,
            'message' => 'Ticket is valid',
            'ticket' => $ticket
        ];
    }

    public function markAsUsed($id)
    {
        $ticket = $this->find($id);
        $ticket->update([
            'is_used' => true,
            'used_at' => now()
        ]);
        return $ticket;
    }
}
