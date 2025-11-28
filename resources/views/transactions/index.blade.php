<!-- resources/views/transactions/index.blade.php -->
@extends('layouts.app')

@section('title', 'My Transactions')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
    <h1 class="text-3xl font-bold mb-8">Transaksi</h1>

    @if($transactions->count() > 0)
    <div class="space-y-4">
        @foreach($transactions as $transaction)
        <div class="bg-white rounded-lg shadow-lg p-6">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h3 class="text-lg font-bold text-gray-900">{{ $transaction->transaction_code }}</h3>
                    <p class="text-sm text-gray-600">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</p>
                </div>
                <span class="px-4 py-2 rounded-full text-sm font-semibold
                    {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                    {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                    {{ $transaction->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}">
                    {{ ucfirst($transaction->status) }}
                </span>
            </div>

            @foreach($transaction->items as $item)
            <div class="flex items-center gap-4 mb-4 pb-4 border-b">
                <img src="{{ $item->event->image ? Storage::url($item->event->image) : 'https://via.placeholder.com/100x75' }}" 
                     alt="{{ $item->event->title }}" 
                     class="w-24 h-18 object-cover rounded-lg">
                <div class="flex-1">
                    <h4 class="font-bold">{{ $item->event->title }}</h4>
                    <p class="text-sm text-gray-600">{{ $item->event->event_date->format('d M Y, H:i') }} WIB</p>
                    <p class="text-sm text-gray-600">{{ $item->event->location }}</p>
                    <p class="text-sm text-gray-600">Quantity: {{ $item->quantity }} ticket(s)</p>
                </div>
                <div class="text-right">
                    <p class="text-lg font-bold text-indigo-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</p>
                </div>
            </div>
            @endforeach

            <div class="flex justify-between items-center pt-4">
                <div>
                    <p class="text-sm text-gray-600">Total Payment</p>
                    <p class="text-2xl font-bold text-indigo-600">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</p>
                </div>
                <a href="{{ route('invoice.show', $transaction->id) }}" 
                   class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
                    View Details
                </a>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="bg-white rounded-lg shadow-lg p-12 text-center">
        <svg class="w-20 h-20 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-xl font-bold text-gray-700 mb-2">Belum Ada Transaksi</h3>
        <p class="text-gray-500 mb-6">You haven't made any ticket purchases yet</p>
        <a href="{{ route('home') }}" 
           class="inline-block bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition">
            Browse Events
        </a>
    </div>
    @endif
</div>
@endsection
