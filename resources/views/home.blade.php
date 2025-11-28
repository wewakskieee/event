@extends('layouts.app')

@section('title', 'Home - EventTix')

@section('content')

<!-- Hero Banner Section dengan Countdown -->
<div class="relative bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-500 text-white overflow-hidden">
    @if($banners->count() > 0)
    <!-- Banner Slider dengan Alpine.js -->
    <div x-data="{ 
        currentSlide: 0, 
        slides: {{ $banners->count() }},
        autoplay: null,
        init() {
            this.autoplay = setInterval(() => {
                this.currentSlide = (this.currentSlide + 1) % this.slides;
            }, 5000);
        }
    }" class="relative h-[500px]">
        
        @foreach($banners as $index => $banner)
        <!-- Slide Item -->
        <div x-show="currentSlide === {{ $index }}"
             x-cloak
             x-transition:enter="transition ease-out duration-1000"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-500"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0">
            
            <!-- Background Image dengan Overlay -->
            @if($banner->image)
            <img src="{{ Storage::url($banner->image) }}" 
                 alt="{{ $banner->event->title }}"
                 class="absolute inset-0 w-full h-full object-cover"
                 onerror="this.src='https://via.placeholder.com/1200x500?text=Event+Banner'">
            @else
            <div class="absolute inset-0 w-full h-full bg-gradient-to-r from-indigo-600 to-purple-600"></div>
            @endif
            
            <!-- Dark Overlay untuk Text Readability -->
            <div class="absolute inset-0 bg-gradient-to-r from-black/80 via-black/60 to-black/40"></div>
            
            <!-- Content dengan Animasi -->
            <div class="relative h-full flex items-center z-10">
                <div class="container mx-auto px-4 sm:px-6 lg:px-8">
                    <div class="max-w-2xl">
                        <!-- Animated Title -->
                        <h1 class="text-5xl md:text-6xl font-bold mb-4 drop-shadow-lg animate-fade-in-up">
                            {{ $banner->event->title }}
                        </h1>
                        
                        <!-- Animated Description -->
                        <p class="text-xl md:text-2xl mb-4 animate-fade-in-up animation-delay-200">
                            📍 {{ $banner->event->location }}
                        </p>
                        
                        <p class="text-lg mb-6 animate-fade-in-up animation-delay-300">
                            🗓️ {{ $banner->event->event_date->format('d M Y, H:i') }} WIB
                        </p>
                        
                        <!-- Price & CTA -->
                        <div class="flex items-center gap-4 flex-wrap animate-fade-in-up animation-delay-400">
                            <div class="bg-yellow-400 text-gray-900 px-6 py-3 rounded-lg font-bold text-xl shadow-lg">
                                Rp {{ number_format($banner->event->price, 0, ',', '.') }}
                            </div>
                            <a href="{{ route('event.detail', $banner->event->slug) }}" 
                               class="bg-white text-indigo-600 px-8 py-3 rounded-lg hover:bg-gray-100 transition font-bold text-lg shadow-lg transform hover:scale-105">
                                Book Now →
                            </a>
                        </div>
                        
                        <!-- Quota Warning -->
                        @if($banner->event->quota_remaining < 20)
                        <div class="mt-4 bg-red-500 text-white px-4 py-2 rounded-lg inline-block animate-pulse">
                            🔥 Almost Sold Out! Only {{ $banner->event->quota_remaining }} tickets left
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        
        <!-- Navigation Dots -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 flex gap-3 z-20">
            @foreach($banners as $index => $banner)
            <button @click="currentSlide = {{ $index }}"
                    :class="currentSlide === {{ $index }} ? 'bg-white w-8' : 'bg-white/50 w-3'"
                    class="h-3 rounded-full transition-all duration-300 hover:bg-white/80"></button>
            @endforeach
        </div>
        
        <!-- Arrow Navigation -->
        @if($banners->count() > 1)
        <button @click="currentSlide = (currentSlide - 1 + slides) % slides" 
                class="absolute left-4 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/40 text-white p-3 rounded-full backdrop-blur-sm transition z-20 text-2xl w-12 h-12 flex items-center justify-center">
            ←
        </button>
        <button @click="currentSlide = (currentSlide + 1) % slides" 
                class="absolute right-4 top-1/2 transform -translate-y-1/2 bg-white/20 hover:bg-white/40 text-white p-3 rounded-full backdrop-blur-sm transition z-20 text-2xl w-12 h-12 flex items-center justify-center">
            →
        </button>
        @endif
    </div>
    @else
    <!-- Fallback jika tidak ada banner -->
    <div class="h-[500px] flex items-center justify-center">
        <div class="text-center">
            <h1 class="text-5xl font-bold mb-4">🎫 EventTix</h1>
            <p class="text-xl">Your Gateway to Amazing Events</p>
        </div>
    </div>
    @endif
</div>

<!-- Countdown Section untuk Event Terdekat -->
@if($upcomingEvent)
<div class="bg-gradient-to-r from-blue-500 to-red-500 text-white py-8">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Next Event Starting In</h2>
        <h3 class="text-2xl mb-4">{{ $upcomingEvent->title }}</h3>
        
        <!-- Countdown Timer -->
        <div x-data="countdown('{{ $upcomingEvent->event_date->toIso8601String() }}')" 
             class="flex justify-center gap-6 flex-wrap">
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-6 min-w-[100px]">
                <div class="text-4xl font-bold" x-text="days"></div>
                <div class="text-sm uppercase">Days</div>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-6 min-w-[100px]">
                <div class="text-4xl font-bold" x-text="hours"></div>
                <div class="text-sm uppercase">Hours</div>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-6 min-w-[100px]">
                <div class="text-4xl font-bold" x-text="minutes"></div>
                <div class="text-sm uppercase">Minutes</div>
            </div>
            <div class="bg-white/20 backdrop-blur-sm rounded-lg p-6 min-w-[100px]">
                <div class="text-4xl font-bold" x-text="seconds"></div>
                <div class="text-sm uppercase">Seconds</div>
            </div>
        </div>
        
        <a href="{{ route('event.detail', $upcomingEvent->slug) }}" 
           class="mt-6 inline-block bg-white text-red-600 px-8 py-3 rounded-lg hover:bg-gray-100 transition font-bold text-lg shadow-lg">
            Get Tickets Now
        </a>
    </div>
</div>
@endif

<!-- Trending Events Section -->
@if($trendingEvents->count() > 0)
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8">
            <h2 class="text-4xl font-bold mb-2">🔥 Trending Events</h2>
            <p class="text-gray-600">Most popular events this week</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($trendingEvents as $index => $event)
            <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 transition duration-300 relative">
                <!-- Trending Badge -->
                <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-sm font-bold z-10 flex items-center gap-1">
                    🔥 #{{ $index + 1 }} Trending
                </div>
                
                <!-- Event Image -->
                <div class="relative h-48 bg-gray-200">
                    @if($event->image)
                    <img src="{{ Storage::url($event->image) }}" 
                         alt="{{ $event->title }}"
                         class="w-full h-full object-cover"
                         onerror="this.src='https://via.placeholder.com/400x200?text=Event+Image'">
                    @else
                    <img src="https://via.placeholder.com/400x200?text={{ urlencode($event->title) }}" 
                         alt="{{ $event->title }}"
                         class="w-full h-full object-cover">
                    @endif
                </div>
                
                <div class="p-6">
                    <h3 class="font-bold text-xl mb-2">{{ $event->title }}</h3>
                    <p class="text-gray-600 text-sm mb-2 line-clamp-2">{{ $event->description }}</p>
                    <p class="text-gray-600 text-sm mb-3">
                        📍 {{ $event->location }}<br>
                        🗓️ {{ $event->event_date->format('d M Y') }}
                    </p>
                    
                    <div class="flex justify-between items-center">
                        <span class="text-2xl font-bold text-indigo-600">
                            Rp {{ number_format($event->price, 0, ',', '.') }}
                        </span>
                        <a href="{{ route('event.detail', $event->slug) }}" 
                           class="bg-indigo-600 text-white px-4 py-2 rounded-lg hover:bg-indigo-700 transition">
                            Book Now
                        </a>
                    </div>
                    
                    <!-- Sold Info -->
                    <div class="mt-3 text-sm text-gray-500">
                        ⭐ {{ $event->tickets_count }} tickets sold
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- All Events Section -->
<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <h2 class="text-3xl font-bold mb-2">All Events</h2>
        <p class="text-gray-600">Browse all available events</p>
    </div>
    
    @if($events->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($events as $event)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden transform hover:scale-105 hover:shadow-2xl transition duration-300">
            <!-- Event Image -->
            <div class="relative h-48 bg-gray-200">
                @if($event->image)
                <img src="{{ Storage::url($event->image) }}" 
                     alt="{{ $event->title }}"
                     class="w-full h-full object-cover"
                     onerror="this.src='https://via.placeholder.com/400x200?text=Event+Image'">
                @else
                <img src="https://via.placeholder.com/400x200?text={{ urlencode($event->title) }}" 
                     alt="{{ $event->title }}"
                     class="w-full h-full object-cover">
                @endif
                
                <!-- Status Badge -->
                @if($event->quota_remaining < 20)
                <div class="absolute top-3 right-3 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold animate-pulse">
                    🔥 {{ $event->quota_remaining }} Left
                </div>
                @endif
            </div>
            
            <!-- Event Content -->
            <div class="p-6">
                <h3 class="font-bold text-xl mb-2 line-clamp-2">{{ $event->title }}</h3>
                <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $event->description }}</p>
                
                <div class="space-y-2 mb-4">
                    <p class="text-gray-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        {{ $event->location }}
                    </p>
                    <p class="text-gray-600 text-sm flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        {{ $event->event_date->format('d M Y, H:i') }} WIB
                    </p>
                </div>
                
                <div class="flex justify-between items-center pt-4 border-t">
                    <div>
                        <p class="text-xs text-gray-500">Starting from</p>
                        <p class="text-2xl font-bold text-indigo-600">
                            Rp {{ number_format($event->price, 0, ',', '.') }}
                        </p>
                    </div>
                    <a href="{{ route('event.detail', $event->slug) }}" 
                       class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition font-semibold">
                        View Event →
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="mt-8">
        {{ $events->links() }}
    </div>
    
    @else
    <div class="text-center py-12">
        <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
        </svg>
        <h3 class="text-xl font-bold text-gray-700 mb-2">No Events Available</h3>
        <p class="text-gray-500">Please check back later for upcoming events</p>
    </div>
    @endif
</div>

<!-- Countdown Script -->
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

<!-- Custom CSS Animations -->
<style>
[x-cloak] { 
    display: none !important; 
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fadeInUp 0.6s ease-out forwards;
}

.animation-delay-200 {
    animation-delay: 0.2s;
    opacity: 0;
}

.animation-delay-300 {
    animation-delay: 0.3s;
    opacity: 0;
}

.animation-delay-400 {
    animation-delay: 0.4s;
    opacity: 0;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

@endsection
