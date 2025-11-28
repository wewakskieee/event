@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold">Dashboard</h1>
    <p class="text-gray-600">Welcome back, {{ auth()->user()->name }}!</p>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-indigo-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Events</p>
                <p class="text-3xl font-bold">{{ $stats['total_events'] }}</p>
            </div>
            <div class="bg-indigo-100 p-4 rounded-full">
                <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-green-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Tickets Sold</p>
                <p class="text-3xl font-bold">{{ $stats['sold_tickets'] }}</p>
            </div>
            <div class="bg-green-100 p-4 rounded-full">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-yellow-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Transactions</p>
                <p class="text-3xl font-bold">{{ $stats['total_transactions'] }}</p>
            </div>
            <div class="bg-yellow-100 p-4 rounded-full">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-9 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-purple-600">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm">Total Revenue</p>
                <p class="text-2xl font-bold">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="bg-purple-100 p-4 rounded-full">
                <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
    <a href="{{ route('admin.events.create') }}" 
       class="bg-indigo-600 text-white p-6 rounded-lg hover:bg-indigo-700 transition text-center">
        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        <h3 class="font-bold text-lg">Create New Event</h3>
    </a>

    <a href="{{ route('admin.banners.create') }}" 
       class="bg-green-600 text-white p-6 rounded-lg hover:bg-green-700 transition text-center">
        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="font-bold text-lg">Upload Banner</h3>
    </a>

    <a href="{{ route('ticket.scan') }}" 
       class="bg-purple-600 text-white p-6 rounded-lg hover:bg-purple-700 transition text-center">
        <svg class="w-12 h-12 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
        </svg>
        <h3 class="font-bold text-lg">Scan Tickets</h3>
    </a>
</div>

<!-- Event Statistics Table -->
<div class="bg-white rounded-lg shadow-lg p-6 mt-6">
    <h2 class="text-xl font-bold mb-4">📊 Event Sales Statistics</h2>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Quota</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tickets Sold</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($events as $event)
                <tr>
                    <td class="px-6 py-4 font-semibold">{{ $event->title }}</td>
                    <td class="px-6 py-4">{{ $event->quota }}</td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full text-sm {{ $event->quota_remaining < 10 ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' }}">
                            {{ $event->quota_remaining }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="font-bold text-indigo-600">{{ $event->tickets_sold ?? 0 }}</span>
                    </td>
                    <td class="px-6 py-4">
                        @php
                            $percentage = $event->quota > 0 ? ($event->tickets_sold / $event->quota) * 100 : 0;
                        @endphp
                        <div class="w-full bg-gray-200 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="text-xs text-gray-600">{{ number_format($percentage, 1) }}% sold</span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
