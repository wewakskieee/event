@extends('layouts.app')

@section('title', 'Invoice - ' . $transaction->transaction_code)

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-block bg-green-100 text-green-800 px-4 py-2 rounded-full mb-4">
                <svg class="inline w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Transaction Successful
            </div>
            <h1 class="text-3xl font-bold mb-2">Invoice</h1>
            <p class="text-gray-600">Transaction Code: <strong>{{ $transaction->transaction_code }}</strong></p>
        </div>

        <!-- Transaction Details -->
        <div class="border-t border-b py-6 mb-6">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Transaction Date</p>
                    <p class="font-semibold">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="font-semibold">
                        <span class="px-3 py-1 rounded-full text-sm
                            {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                            {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                            {{ $transaction->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}">
                            {{ ucfirst($transaction->status) }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Customer Name</p>
                    <p class="font-semibold">{{ $transaction->user->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Email</p>
                    <p class="font-semibold">{{ $transaction->user->email }}</p>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <h3 class="font-bold text-lg mb-4">Order Details</h3>
            @foreach($transaction->items as $item)
            <div class="flex items-center justify-between mb-4 pb-4 border-b">
                <div class="flex items-center gap-4">
                    <img src="{{ $item->event->image ? Storage::url($item->event->image) : 'https://via.placeholder.com/100x75' }}" 
                         alt="{{ $item->event->title }}" 
                         class="w-20 h-15 object-cover rounded-lg">
                    <div>
                        <h4 class="font-bold">{{ $item->event->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $item->event->event_date->format('d M Y, H:i') }} WIB</p>
                        <p class="text-sm text-gray-600">{{ $item->event->location }}</p>
                        <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }}</p>
                    </div>
                </div>
                <div class="text-right">
                    <p class="font-semibold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Total -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="flex justify-between text-2xl font-bold">
                <span>Total Payment:</span>
                <span class="text-indigo-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</span>
            </div>
        </div>

        @if($transaction->status === 'pending')
        <!-- Payment Instructions -->
        <div class="bg-yellow-50 border border-yellow-200 p-6 rounded-lg mb-6">
            <h4 class="font-bold text-lg mb-3 text-yellow-800">Payment Instructions</h4>
            <p class="text-yellow-700 mb-4">Please complete your payment to activate your tickets.</p>
            <div class="space-y-2 text-sm text-yellow-700">
                <p>1. Transfer to: <strong>BCA 1234567890</strong> (EventTix Official)</p>
                <p>2. Amount: <strong>Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</strong></p>
                <p>3. Note: <strong>{{ $transaction->transaction_code }}</strong></p>
                <p>4. Send proof to: <strong>payment@eventtix.com</strong></p>
            </div>
        </div>
        @endif
    </div>

    <!-- Tickets -->
    @if($transaction->status === 'paid')
    <div class="bg-white rounded-lg shadow-lg p-8">
        <h2 class="text-2xl font-bold mb-6 text-center">Your Digital Tickets</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($transaction->tickets as $ticket)
            <div class="border-2 border-indigo-200 rounded-lg p-6 bg-gradient-to-br from-indigo-50 to-white">
                <div class="text-center mb-4">
                    <h3 class="font-bold text-lg mb-2">{{ $ticket->event->title }}</h3>
                    <p class="text-sm text-gray-600">{{ $ticket->event->event_date->format('d M Y, H:i') }} WIB</p>
                    <p class="text-sm text-gray-600">{{ $ticket->event->location }}</p>
                </div>

                <!-- QR Code -->
                <div class="bg-white p-4 rounded-lg mb-4 flex justify-center">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ $ticket->qr_code }}" 
                         alt="QR Code" 
                         class="w-48 h-48">
                </div>

                <div class="text-center">
                    <p class="text-sm text-gray-600">Ticket Code</p>
                    <p class="font-mono font-bold text-lg">{{ $ticket->ticket_code }}</p>
                    
                    @if($ticket->is_used)
                    <div class="mt-3 bg-red-100 text-red-800 px-3 py-2 rounded-lg text-sm">
                        ✓ Used on {{ $ticket->used_at->format('d M Y, H:i') }}
                    </div>
                    @else
                    <div class="mt-3 bg-green-100 text-green-800 px-3 py-2 rounded-lg text-sm">
                        ✓ Valid - Not Used
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-6 text-center">
            <button onclick="window.print()" 
                    class="bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
                🖨️ Print Tickets
            </button>
        </div>
    </div>
    @endif
</div>

<style>
    @media print {
        nav, footer, button { display: none !important; }
        body { background: white; }
    }
</style>
@endsection
