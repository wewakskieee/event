<?php
// app/Services/TicketService.php

namespace App\Services;

use App\Repositories\Contracts\TicketRepositoryInterface;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketService
{
    protected $ticketRepository;

    public function __construct(TicketRepositoryInterface $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function getAllTickets()
    {
        return $this->ticketRepository->all();
    }

    public function getTicketByCode($code)
    {
        return $this->ticketRepository->findByCode($code);
    }

    public function validateTicket($qrCode)
    {
        return $this->ticketRepository->validateTicket($qrCode);
    }

    public function useTicket($qrCode)
    {
        $validation = $this->validateTicket($qrCode);

        if (!$validation['valid']) {
            return $validation;
        }

        $ticket = $this->ticketRepository->markAsUsed($validation['ticket']->id);

        return [
            'valid' => true,
            'message' => 'Ticket successfully validated and marked as used',
            'ticket' => $ticket
        ];
    }

    public function generateQrCode($ticketCode)
    {
        return QrCode::size(300)->generate($ticketCode);
    }
}
