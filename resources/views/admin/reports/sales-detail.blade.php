<!-- resources/views/admin/reports/sales-detail.blade.php -->
@extends('layouts.admin')

@section('title', 'Sales Detail Report')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Sales Detail Report</h1>
    <p class="text-gray-600">Detailed transaction report with multi-join query</p>
</div>

<!-- Summary Cards -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600 mb-1">Total Transactions</div>
        <div class="text-3xl font-bold text-blue-600">{{ number_format($summary->total_transactions) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600 mb-1">Total Customers</div>
        <div class="text-3xl font-bold text-green-600">{{ number_format($summary->total_customers) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600 mb-1">Tickets Sold</div>
        <div class="text-3xl font-bold text-purple-600">{{ number_format($summary->total_tickets_sold) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600 mb-1">Total Revenue</div>
        <div class="text-3xl font-bold text-indigo-600">Rp {{ number_format($summary->total_revenue, 0, ',', '.') }}</div>
    </div>
</div>

<!-- Filters -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('admin.reports.sales-detail') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
            <label class="block text-sm font-semibold mb-2">Date From</label>
            <input type="date" name="date_from" value="{{ request('date_from') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-2">Date To</label>
            <input type="date" name="date_to" value="{{ request('date_to') }}" class="w-full border rounded px-3 py-2">
        </div>
        <div>
            <label class="block text-sm font-semibold mb-2">Event</label>
            <select name="event_id" class="w-full border rounded px-3 py-2">
                <option value="">All Events</option>
                @foreach($events as $event)
                <option value="{{ $event->id }}" {{ request('event_id') == $event->id ? 'selected' : '' }}>
                    {{ $event->title }}
                </option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Filter
            </button>
            <a href="{{ route('admin.reports.sales-detail') }}" class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Transaction</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tickets</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($salesDetail as $sale)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="font-semibold text-sm">{{ $sale->transaction_code }}</div>
                        <div class="text-xs text-gray-500">{{ $sale->transaction_status }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ \Carbon\Carbon::parse($sale->transaction_date)->format('d M Y H:i') }}
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium">{{ $sale->customer_name }}</div>
                        <div class="text-xs text-gray-500">{{ $sale->customer_email }}</div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium">{{ $sale->event_title }}</div>
                        <div class="text-xs text-gray-500">{{ $sale->event_location }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        {{ $sale->quantity }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        Rp {{ number_format($sale->ticket_price, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                        Rp {{ number_format($sale->subtotal, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        {{ $sale->tickets_generated }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    
    <div class="px-6 py-4 border-t">
        {{ $salesDetail->links() }}
    </div>
</div>
@endsection
