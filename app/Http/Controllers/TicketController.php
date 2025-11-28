<?php
// app/Http/Controllers/TicketController.php

namespace App\Http\Controllers;

use App\Services\TicketService;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    protected $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }

    public function scanPage()
    {
        return view('ticket.scan');
    }

    public function validate(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        try {
            $result = $this->ticketService->validateTicket($validated['qr_code']);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Ticket not found',
            ], 404);
        }
    }

    public function use(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        try {
            $result = $this->ticketService->useTicket($validated['qr_code']);

            return response()->json($result);
        } catch (\Exception $e) {
            return response()->json([
                'valid' => false,
                'message' => 'Ticket not found',
            ], 404);
        }
    }

    public function generateQr($ticketCode)
    {
        $qrCode = $this->ticketService->generateQrCode($ticketCode);

        return response($qrCode)->header('Content-Type', 'image/svg+xml');
    }
}
