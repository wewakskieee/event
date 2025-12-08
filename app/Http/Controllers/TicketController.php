<?php

namespace App\Http\Controllers;

use App\Services\TicketService;
use Illuminate\Http\Request;
use App\Models\Ticket;

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

    \Log::info('Attempting to use ticket', ['qr_code' => $validated['qr_code']]);

    try {
        $input = $validated['qr_code'];
        $ticket = Ticket::with(['event', 'user', 'transaction'])
            ->where(function($query) use ($input) {
                $query->where('qr_code', $input)
                      ->orWhere('ticket_code', $input);
            })
            ->first();

        if (!$ticket) {
            \Log::warning('Ticket not found', ['input' => $input]);
            return response()->json([
                'valid' => false,
                'message' => 'Ticket not found',
            ], 404);
        }

        \Log::info('Ticket found', [
            'id' => $ticket->id,
            'is_used' => $ticket->is_used,
            'transaction_status' => $ticket->transaction->status
        ]);

        if ($ticket->transaction->status !== 'paid') {
            return response()->json([
                'valid' => false,
                'message' => 'Transaction not paid yet',
                'ticket' => $ticket
            ]);
        }

        try {
            \Log::info('Attempting to mark ticket as used', ['ticket_id' => $ticket->id]);
            
            $ticket->is_used = true;
            $ticket->save();
            
            \Log::info('Ticket marked as used successfully', ['ticket_id' => $ticket->id]);
            
            return response()->json([
                'valid' => true,
                'message' => 'Ticket successfully validated and marked as used',
                'ticket' => $ticket->fresh()
            ]);
            
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Database error when using ticket', [
                'ticket_id' => $ticket->id,
                'error' => $e->getMessage()
            ]);
            
            if (str_contains($e->getMessage(), 'already scanned')) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Ticket already scanned and cannot be scanned again',
                    'ticket' => $ticket
                ], 400);
            }
            
            return response()->json([
                'valid' => false,
                'message' => 'Database error: ' . $e->getMessage(),
            ], 500);
        }

    } catch (\Exception $e) {
        \Log::error('Unexpected error', ['error' => $e->getMessage()]);
        
        return response()->json([
            'valid' => false,
            'message' => 'Failed to use ticket: ' . $e->getMessage(),
        ], 500);
    }
}
}
