@extends('layouts.admin')

@section('title', 'Transaction Tickets - ' . $transaction->transaction_code)

@section('content')
<div class="mb-6">
    <a href="{{ route('admin.transactions.index') }}" class="text-indigo-600 hover:text-indigo-800 mb-4 inline-block">
        ← Back to Transactions
    </a>
    <h1 class="text-2xl font-bold">Transaction Tickets</h1>
    <p class="text-gray-600">{{ $transaction->transaction_code }}</p>
</div>

<!-- Transaction Info -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <p class="text-sm text-gray-600">Customer</p>
            <p class="font-semibold">{{ $transaction->user->name }}</p>
            <p class="text-sm text-gray-500">{{ $transaction->user->email }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Total Amount</p>
            <p class="font-semibold text-lg">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-600">Status</p>
            <span class="px-3 py-1 rounded-full text-xs font-semibold
                {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                {{ $transaction->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}">
                {{ ucfirst($transaction->status) }}
            </span>
        </div>
    </div>
</div>

<!-- Tickets List -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    @foreach($transaction->tickets as $ticket)
    <div class="bg-white rounded-lg shadow-lg p-6 border-2 {{ $ticket->is_used ? 'border-red-300' : 'border-green-300' }}">
        <div class="text-center mb-4">
            <h3 class="font-bold text-lg mb-2">{{ $ticket->event->title }}</h3>
            <p class="text-sm text-gray-600">{{ $ticket->event->event_date->format('d M Y, H:i') }} WIB</p>
        </div>

        <!-- QR Code Image -->
        <div class="bg-gray-100 p-4 rounded-lg mb-4 flex justify-center">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $ticket->qr_code }}" 
                 alt="QR Code" 
                 class="w-48 h-48">
        </div>

        <!-- Ticket Code -->
        <div class="mb-3 bg-blue-50 p-3 rounded-lg">
            <p class="text-xs text-gray-600 mb-1">Ticket Code:</p>
            <div class="flex items-center justify-between">
                <p class="font-mono font-bold text-sm">{{ $ticket->ticket_code }}</p>
                <button onclick="copyToClipboard('{{ $ticket->ticket_code }}')" 
                        class="text-xs bg-blue-600 text-white px-3 py-1 rounded hover:bg-blue-700">
                    📋 Copy
                </button>
            </div>
        </div>

        <!-- QR Code Hash -->
        <div class="mb-3 bg-yellow-50 p-3 rounded-lg">
            <p class="text-xs text-gray-600 mb-1 font-semibold">QR Code Hash (untuk Scanner):</p>
            <div class="bg-white p-2 rounded border mb-2">
                <p class="font-mono text-xs break-all select-all">{{ $ticket->qr_code }}</p>
            </div>
            <button onclick="copyToClipboard('{{ $ticket->qr_code }}')" 
                    class="w-full text-xs bg-yellow-600 text-white px-3 py-2 rounded hover:bg-yellow-700 font-semibold">
                📋 Copy QR Hash
            </button>
        </div>

        <!-- Status -->
        <div class="text-center">
            @if($ticket->is_used)
                <div class="bg-red-100 text-red-800 px-4 py-3 rounded-lg">
                    <p class="font-bold">✓ USED</p>
                    <p class="text-sm">{{ $ticket->used_at->format('d M Y, H:i') }} WIB</p>
                </div>
            @else
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded-lg">
                    <p class="font-bold">✓ VALID</p>
                    <p class="text-sm">Not Used Yet</p>
                </div>
            @endif
        </div>
    </div>
    @endforeach
</div>

<!-- Quick Actions -->
<div class="mt-6 bg-indigo-50 border border-indigo-200 rounded-lg p-6 text-center">
    <h3 class="font-bold text-lg mb-4">🎫 Quick Actions</h3>
    <div class="flex gap-4 justify-center flex-wrap">
        <a href="{{ route('ticket.scan') }}" target="_blank"
           class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-semibold">
            📱 Open Scanner
        </a>
        <a href="{{ route('invoice.show', $transaction->id) }}" target="_blank"
           class="bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition font-semibold">
            🧾 View Invoice
        </a>
        <button onclick="window.print()" 
                class="bg-gray-600 text-white px-6 py-3 rounded-lg hover:bg-gray-700 transition font-semibold">
            🖨️ Print Tickets
        </button>
    </div>
</div>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity';
        toast.innerHTML = '✓ Copied: ' + text.substring(0, 20) + '...';
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 2000);
    }, function(err) {
        alert('Failed to copy: ' + err);
    });
}
</script>

<style>
@media print {
    nav, footer, button, .bg-indigo-50, a[href*="scan"], a[href*="invoice"] { 
        display: none !important; 
    }
    .grid {
        page-break-inside: avoid;
    }
}
</style>
@endsection
