@extends('layouts.admin')

@section('title', 'Manage Transactions')

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold">Transactions Management</h1>
    <p class="text-gray-600">Manage and update transaction status</p>
</div>

<!-- Filter Tabs -->
<div class="mb-6 flex gap-2 flex-wrap">
    <a href="?status=all" 
       class="px-4 py-2 rounded-lg transition {{ request('status', 'all') == 'all' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
        All ({{ $stats['total'] }})
    </a>
    <a href="?status=pending" 
       class="px-4 py-2 rounded-lg transition {{ request('status') == 'pending' ? 'bg-yellow-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
        Pending ({{ $stats['pending'] }})
    </a>
    <a href="?status=paid" 
       class="px-4 py-2 rounded-lg transition {{ request('status') == 'paid' ? 'bg-green-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
        Paid ({{ $stats['paid'] }})
    </a>
    <a href="?status=canceled" 
       class="px-4 py-2 rounded-lg transition {{ request('status') == 'canceled' ? 'bg-red-600 text-white' : 'bg-white text-gray-700 hover:bg-gray-100' }}">
        Canceled ({{ $stats['canceled'] }})
    </a>
</div>

@if($transactions->count() > 0)
<div class="bg-white rounded-lg shadow overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction Code</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($transactions as $transaction)
            <tr class="hover:bg-gray-50">
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-mono font-semibold text-indigo-600">{{ $transaction->transaction_code }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-semibold">{{ $transaction->user->name }}</div>
                    <div class="text-sm text-gray-500">{{ $transaction->user->email }}</div>
                </td>
                <td class="px-6 py-4">
                    @foreach($transaction->items as $item)
                        <div class="text-sm font-medium">{{ $item->event->title }}</div>
                        <div class="text-xs text-gray-500">{{ $item->quantity }} ticket(s)</div>
                    @endforeach
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <div class="font-bold text-lg text-gray-900">Rp {{ number_format($transaction->total_amount, 0, ',', '.') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 rounded-full text-xs font-semibold
                        {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $transaction->status === 'canceled' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($transaction->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div>{{ $transaction->created_at->format('d M Y') }}</div>
                    <div class="text-xs">{{ $transaction->created_at->format('H:i') }} WIB</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <div class="flex gap-2">

                        @if($transaction->status === 'pending')
                            <form action="{{ route('admin.transactions.update-status', $transaction->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="paid">
                                <button type="submit" onclick="return confirm('Mark this transaction as PAID?')" class="bg-green-600 text-white px-3 py-1 rounded hover:bg-green-700 transition text-xs font-semibold">
                                    ✓ Paid
                                </button>
                            </form>

                            <form action="{{ route('admin.transactions.update-status', $transaction->id) }}" method="POST" class="inline">
                                @csrf
                                <input type="hidden" name="status" value="canceled">
                                <button type="submit" onclick="return confirm('Cancel this transaction? Quota will be restored.')" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700 transition text-xs font-semibold">
                                    ✗ Cancel
                                </button>
                            </form>
                        @endif

                        @if($transaction->status === 'paid')
                        <a href="{{ route('admin.transactions.tickets', $transaction->id) }}"
                           class="bg-purple-600 text-white px-3 py-1 rounded text-xs hover:bg-purple-700">
                            🎫 Tickets
                        </a>
                        @endif

                        <a href="{{ route('invoice.show', $transaction->id) }}" target="_blank"
                           class="bg-indigo-600 text-white px-3 py-1 rounded text-xs hover:bg-indigo-700">
                            👁 View
                        </a>

                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $transactions->appends(['status' => request('status')])->links() }}
</div>

@else
<div class="bg-white rounded-lg shadow p-12 text-center">
    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Transactions Found</h3>
    <p class="text-gray-500">
        @if(request('status') && request('status') != 'all')
            No {{ request('status') }} transactions available
        @else
            No transactions have been made yet
        @endif
    </p>
</div>
@endif

<!-- Stats Summary -->
<div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-4">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600">Total Transactions</div>
        <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600">Pending</div>
        <div class="text-2xl font-bold text-yellow-600">{{ $stats['pending'] }}</div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600">Paid</div>
        <div class="text-2xl font-bold text-green-600">{{ $stats['paid'] }}</div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600">Canceled</div>
        <div class="text-2xl font-bold text-red-600">{{ $stats['canceled'] }}</div>
    </div>
</div>
@endsection
