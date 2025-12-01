@extends('layouts.app')

@section('title', 'Beranda - EventTix')

@section('content')

<div class="relative bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 text-white overflow-hidden">
    @if($banners->count() > 0)
    <div x-data="{ 
        currentSlide: 0, 
        slides: {{ $banners->count() }},
        autoplay: null,
        init() {
            this.autoplay = setInterval(() => {
                this.currentSlide = (this.currentSlide + 1) % this.slides;
            }, 5000);
        }
    }" class="relative h-[600px]">
        
        @foreach($banners as $index => $banner)
        <div x-show="currentSlide === {{ $index }}"
             x-cloak
             x-transition:enter="transition ease-out duration-1000"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-500"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0">
            
            @if($banner->image)
            <img src="{{ Storage::url($banner->image) }}" 
                 alt="{{ $banner->event->title }}"
                 class="absolute inset-0 w-full h-full object-cover"
                 onerror="this.src='https://via.placeholder.com/1200x600?text=Event+Banner'">
            @else
            <div class="absolute inset-0 w-full h-full bg-gradient-to-br from-blue-600 to-purple-700"></div>
            @endif
            
            <div class="absolute inset-0 bg-gradient-to-r from-slate-900/90 via-slate-900/70 to-slate-900/50"></div>
            
            <div class="relative h-full flex items-center z-10">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-3xl">
                        <div class="inline-block px-4 py-2 bg-blue-500/30 backdrop-blur-sm rounded-full text-sm font-semibold mb-4">
                            Event Unggulan
                        </div>
                        
                        <h1 class="text-5xl md:text-7xl font-bold mb-6 leading-tight">
                            {{ $banner->event->title }}
                        </h1>
                        
                        <div class="flex flex-wrap gap-6 text-lg mb-8">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                {{ $banner->event->location }}
                            </div>
                            <div class="flex items-center">
                                <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                {{ $banner->event->event_date->format('d M Y, H:i') }} WIB
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-4">
                            <div class="bg-white text-slate-900 px-8 py-4 rounded-xl font-bold text-2xl shadow-2xl">
                                Rp {{ number_format($banner->event->price, 0, ',', '.') }}
                            </div>
                            <a href="{{ route('event.detail', $banner->event->slug) }}" 
                               class="px-8 py-4 btn-primary text-white rounded-xl font-bold text-lg shadow-2xl">
                                Pesan Tiket
                            </a>
                        </div>
                        
                        @if($banner->event->quota_remaining < 20)
                        <div class="mt-6 inline-flex items-center px-5 py-3 bg-red-500 text-white rounded-xl font-semibold shadow-lg">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                            </svg>
                            Ketersediaan Terbatas: tersisa {{ $banner->event->quota_remaining }} tiket
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex gap-3 z-20">
            @foreach($banners as $index => $banner)
            <button @click="currentSlide = {{ $index }}"
                    :class="currentSlide === {{ $index }} ? 'bg-white w-10' : 'bg-white/40 w-3'"
                    class="h-3 rounded-full transition-all duration-300 hover:bg-white"></button>
            @endforeach
        </div>
        
        @if($banners->count() > 1)
        <button @click="currentSlide = (currentSlide - 1 + slides) % slides" 
                class="absolute left-6 top-1/2 transform -translate-y-1/2 bg-white/10 hover:bg-white/30 backdrop-blur-md text-white p-4 rounded-full transition z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
        </button>
        <button @click="currentSlide = (currentSlide + 1) % slides" 
                class="absolute right-6 top-1/2 transform -translate-y-1/2 bg-white/10 hover:bg-white/30 backdrop-blur-md text-white p-4 rounded-full transition z-20">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
        </button>
        @endif
    </div>
    @else
    <div class="h-[600px] flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-6xl font-bold mb-4">EventTix</h1>
            <p class="text-2xl text-blue-100">Temukan Event Menarik</p>
        </div>
    </div>
    @endif
</div>

@if($trendingEvents->count() > 0)
<section class="py-20 bg-gray-50">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <div class="inline-block px-4 py-2 bg-red-100 text-red-600 rounded-full text-sm font-semibold mb-4">
                Populer
            </div>
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Event Terpopuler</h2>
            <p class="text-xl text-gray-600">Event paling banyak dipesan minggu ini</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($trendingEvents as $index => $event)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover border border-gray-100">
                <div class="relative">
                    <div class="absolute top-4 left-4 px-4 py-2 bg-red-500 text-white rounded-full text-sm font-bold z-10 shadow-lg">
                        Peringkat #{{ $index + 1 }}
                    </div>
                    
                    @if($event->image)
                    <img src="{{ Storage::url($event->image) }}" 
                         alt="{{ $event->title }}"
                         class="w-full h-56 object-cover"
                         onerror="this.src='https://via.placeholder.com/400x250?text=Event'">
                    @else
                    <div class="w-full h-56 bg-gradient-to-br from-blue-500 to-purple-600"></div>
                    @endif
                </div>
                
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $event->title }}</h3>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $event->description }}</p>
                    
                    <div class="space-y-2 mb-6 text-sm">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $event->location }}
                        </div>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event->event_date->format('d M Y') }}
                        </div>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            {{ $event->tickets_count }} tiket terjual
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100">
    <div>
        <div class="text-xs text-gray-500 mb-0.5">Mulai dari</div>
        <div class="text-xl md:text-2xl font-bold text-blue-600">
            Rp {{ number_format($event->price, 0, ',', '.') }}
        </div>
    </div>
    <a href="{{ route('event.detail', $event->slug) }}" 
       class="px-5 py-2.5 btn-primary text-white rounded-lg text-sm font-semibold shadow-sm hover:shadow-md transition-all">
        Lihat Detail
    </a>
</div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-20 bg-white">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-12">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-4">Semua Event</h2>
            <p class="text-xl text-gray-600">{{ $events->total() }} event tersedia</p>
        </div>
        
        @if($events->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($events as $event)
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden card-hover border border-gray-100">
                <div class="relative">
                    @if($event->quota_remaining < 20)
                    <div class="absolute top-4 right-4 px-4 py-2 bg-red-500 text-white rounded-full text-sm font-bold z-10 shadow-lg animate-pulse">
                        {{ $event->quota_remaining }} Tersisa
                    </div>
                    @endif
                    
                    @if($event->image)
                    <img src="{{ Storage::url($event->image) }}" 
                         alt="{{ $event->title }}"
                         class="w-full h-56 object-cover"
                         onerror="this.src='https://via.placeholder.com/400x250?text=Event'">
                    @else
                    <div class="w-full h-56 bg-gradient-to-br from-blue-500 to-purple-600"></div>
                    @endif
                </div>
                
                <div class="p-6">
                    <h3 class="text-2xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $event->title }}</h3>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $event->description }}</p>
                    
                    <div class="space-y-2 mb-6 text-sm">
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $event->location }}
                        </div>
                        <div class="flex items-center text-gray-600">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event->event_date->format('d M Y, H:i') }} WIB
                        </div>
                    </div>
                    
                    <div class="flex justify-between items-center pt-4 border-t border-gray-100 gap-3">
    <div>
        <div class="text-xs text-gray-500 mb-0.5">Mulai dari</div>
        <div class="text-xl md:text-2xl font-bold text-blue-600">
            Rp {{ number_format($event->price, 0, ',', '.') }}
        </div>
    </div>
    <a href="{{ route('event.detail', $event->slug) }}" 
       class="px-5 py-2.5 btn-primary text-white rounded-lg text-sm font-semibold shadow-md hover:shadow-lg transition-all">
        Lihat Detail
    </a>
</div>
                </div>
            </div>
            @endforeach
        </div>
        
        <div class="mt-12">
            {{ $events->links() }}
        </div>
        
        @else
        <div class="text-center py-20">
            <svg class="w-32 h-32 mx-auto text-gray-300 mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="text-3xl font-bold text-gray-700 mb-3">Tidak Ada Event Tersedia</h3>
            <p class="text-xl text-gray-500">Cek kembali nanti untuk event mendatang</p>
        </div>
        @endif
    </div>
</section>

<script>
function countdown(eventDate) {
    return {
        days: 0,
        hours: 0,
        minutes: 0,
        seconds: 0,
        init() {
            this.updateCountdown();
            setInterval(() => {
                this.updateCountdown();
            }, 1000);
        },
        updateCountdown() {
            const now = new Date().getTime();
            const target = new Date(eventDate).getTime();
            const distance = target - now;
            
            if (distance > 0) {
                this.days = Math.floor(distance / (1000 * 60 * 60 * 24));
                this.hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                this.minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                this.seconds = Math.floor((distance % (1000 * 60)) / 1000);
            } else {
                this.days = 0;
                this.hours = 0;
                this.minutes = 0;
                this.seconds = 0;
            }
        }
    }
}
</script>

<style>
[x-cloak] { 
    display: none !important; 
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

@endsection