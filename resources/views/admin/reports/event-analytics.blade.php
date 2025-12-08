<!-- resources/views/admin/reports/event-analytics.blade.php -->
@extends('layouts.admin')

@section('title', 'Event Analytics')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Event Analytics</h1>
    <p class="text-gray-600">Performance metrics for all events</p>
</div>

<!-- Summary -->
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600 mb-1">Total Events</div>
        <div class="text-3xl font-bold text-blue-600">{{ $summary->total_events }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600 mb-1">Total Tickets Sold</div>
        <div class="text-3xl font-bold text-green-600">{{ number_format($summary->total_tickets_sold) }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600 mb-1">Total Revenue</div>
        <div class="text-3xl font-bold text-purple-600">Rp {{ number_format($summary->total_revenue, 0, ',', '.') }}</div>
    </div>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="text-sm text-gray-600 mb-1">Avg Sold %</div>
        <div class="text-3xl font-bold text-indigo-600">{{ number_format($summary->avg_sold_percentage, 1) }}%</div>
    </div>
</div>

<!-- Filters & Sort -->
<div class="bg-white rounded-lg shadow p-6 mb-6">
    <form action="{{ route('admin.reports.event-analytics') }}" method="GET" class="flex gap-4 items-end">
        <div>
            <label class="block text-sm font-semibold mb-2">Status</label>
            <select name="status" class="border rounded px-3 py-2">
                <option value="">All</option>
                <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-2">Sort By</label>
            <select name="sort" class="border rounded px-3 py-2">
                <option value="tickets_sold" {{ request('sort') == 'tickets_sold' ? 'selected' : '' }}>Tickets Sold</option>
                <option value="total_revenue" {{ request('sort') == 'total_revenue' ? 'selected' : '' }}>Revenue</option>
                <option value="sold_percentage" {{ request('sort') == 'sold_percentage' ? 'selected' : '' }}>Sold %</option>
                <option value="event_date" {{ request('sort') == 'event_date' ? 'selected' : '' }}>Date</option>
            </select>
        </div>
        <div>
            <label class="block text-sm font-semibold mb-2">Order</label>
            <select name="order" class="border rounded px-3 py-2">
                <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Descending</option>
                <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Ascending</option>
            </select>
        </div>
        <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Apply
        </button>
    </form>
</div>

<!-- Table -->
<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sold %</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customers</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($events as $event)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4">
                        <div class="font-semibold text-sm">{{ $event->title }}</div>
                        <div class="text-xs text-gray-500">{{ $event->location }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ $event->quota }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-semibold">{{ $event->tickets_sold }}</div>
                        <div class="text-xs text-gray-500">{{ $event->total_transactions }} transactions</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($event->sold_percentage, 100) }}%"></div>
                            </div>
                            <span class="text-sm font-semibold">{{ number_format($event->sold_percentage, 1) }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold">
                        Rp {{ number_format($event->total_revenue, 0, ',', '.') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center">
                        {{ $event->unique_customers }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
