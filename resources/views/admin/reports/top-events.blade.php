<!-- resources/views/admin/reports/top-events.blade.php -->
@extends('layouts.admin')

@section('title', 'Top Performing Events')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Top Performing Events</h1>
    <p class="text-gray-600">Events ranked by ticket sales (Nested Subquery + Join)</p>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gradient-to-r from-blue-600 to-purple-600 text-white">
                <tr>
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">Rank</th>
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">Event</th>
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">Date</th>
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">Tickets Sold</th>
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">Sold %</th>
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">Revenue</th>
                    <th class="px-6 py-4 text-left text-sm font-bold uppercase">Transactions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @foreach($topEvents as $event)
                <tr class="hover:bg-gray-50 {{ $event->sales_rank <= 3 ? 'bg-yellow-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            @if($event->sales_rank == 1)
                            <span class="text-3xl">🥇</span>
                            @elseif($event->sales_rank == 2)
                            <span class="text-3xl">🥈</span>
                            @elseif($event->sales_rank == 3)
                            <span class="text-3xl">🥉</span>
                            @else
                            <span class="text-2xl font-bold text-gray-500">#{{ $event->sales_rank }}</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="font-bold text-lg">{{ $event->title }}</div>
                        <div class="text-sm text-gray-500">{{ $event->location }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        {{ \Carbon\Carbon::parse($event->event_date)->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-2xl font-bold text-blue-600">{{ number_format($event->tickets_sold) }}</div>
                        <div class="text-xs text-gray-500">of {{ $event->quota }} quota</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <div class="w-24 bg-gray-200 rounded-full h-3 mr-2">
                                <div class="bg-green-500 h-3 rounded-full" style="width: {{ min($event->sold_percentage, 100) }}%"></div>
                            </div>
                            <span class="text-sm font-bold">{{ number_format($event->sold_percentage, 1) }}%</span>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-lg font-bold text-green-600">
                            Rp {{ number_format($event->total_revenue, 0, ',', '.') }}
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-center">
                        <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-full text-sm font-semibold">
                            {{ $event->total_transactions }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
