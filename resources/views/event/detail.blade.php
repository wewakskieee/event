@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <!-- Banner -->
    <div class="mb-8 rounded-2xl overflow-hidden shadow-2xl">
        <img src="{{ $event->image ? Storage::url($event->image) : 'https://via.placeholder.com/1200x500' }}" 
             alt="{{ $event->title }}" 
             class="w-full h-96 object-cover">
    </div>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-4xl font-bold mb-4">{{ $event->title }}</h1>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <div class="flex items-center text-gray-700 mb-3">
                    <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-gray-500">Date & Time</p>
                        <p class="font-semibold">{{ $event->event_date->format('l, d F Y • H:i') }} WIB</p>
                    </div>
                </div>
                
                <div class="flex items-center text-gray-700 mb-3">
                    <svg class="w-6 h-6 mr-3 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    <div>
                        <p class="text-sm text-gray-500">Location</p>
                        <p class="font-semibold">{{ $event->location }}</p>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-indigo-50 p-6 rounded-lg">
                    <p class="text-sm text-gray-600 mb-2">Price per ticket</p>
                    <p class="text-4xl font-bold text-indigo-600 mb-4">
                        Rp {{ number_format($event->price, 0, ',', '.') }}
                    </p>
                    
                    <div class="mb-4">
                        <p class="text-sm text-gray-600">Tickets Available</p>
                        <p class="text-2xl font-bold {{ $event->isAlmostSoldOut() ? 'text-red-600 blink-soft' : 'text-gray-800' }}">
                            {{ $event->quota_remaining }} / {{ $event->quota }}
                        </p>
                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                            <div class="bg-indigo-600 h-2 rounded-full" 
                                 style="width: {{ ($event->quota_remaining / $event->quota) * 100 }}%"></div>
                        </div>
                    </div>

                    @auth
                        @if($event->isSoldOut())
                            <button disabled class="w-full bg-gray-400 text-white px-6 py-3 rounded-lg cursor-not-allowed">
                                Sold Out
                            </button>
                        @else
                            <form action="{{ route('checkout') }}" method="GET">
                                <input type="hidden" name="event_id" value="{{ $event->id }}">
                                <button type="submit" class="w-full bg-indigo-600 text-white px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-bold">
                                    🎫 Buy Tickets
                                </button>
                            </form>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="block w-full bg-indigo-600 text-white text-center px-6 py-3 rounded-lg hover:bg-indigo-700 transition font-bold">
                            Login to Buy Tickets
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <div class="border-t pt-6">
            <h2 class="text-2xl font-bold mb-4">About This Event</h2>
            <div class="prose max-w-none text-gray-700">
                {!! nl2br(e($event->description)) !!}
            </div>
        </div>
    </div>
</div>
@endsection
