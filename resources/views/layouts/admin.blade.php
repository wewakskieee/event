<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin Panel') - EventTix</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>
<body class="bg-gray-100">
<div class="flex h-screen" x-data="{ sidebarOpen: true }">

    <!-- Sidebar -->
    <aside :class="sidebarOpen ? 'w-64' : 'w-20'"
           class="bg-gray-800 text-white transition-all duration-300">
        <div class="p-4">
            <div class="flex items-center justify-between mb-8">
                <h1 x-show="sidebarOpen" class="text-2xl font-bold">EventTix</h1>
                <button @click="sidebarOpen = !sidebarOpen" class="p-2 hover:bg-gray-700 rounded">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 6h16M4 12h16M4 18h16"/>
                    </svg>
                </button>
            </div>

            <nav class="space-y-2">

                <!-- Dashboard -->
                <a href="{{ route('admin.dashboard') }}"
                   class="flex items-center p-3 hover:bg-gray-700 rounded transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">Dashboard</span>
                </a>

                <!-- Reports Dropdown -->
                <!-- Reports Dropdown -->
@php
    $isReportsActive = request()->routeIs('admin.reports.*');
@endphp

<div class="relative" 
     x-data="{ open: {{ $isReportsActive ? 'true' : 'false' }} }">

    <button @click="open = !open"
        class="flex items-center p-3 hover:bg-gray-700 rounded transition w-full
        {{ $isReportsActive ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
        
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                d="M9 17v-6m6 6v-4m2-5H7a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2V10a2 2 0 00-2-2z"/>
        </svg>

        <span x-show="sidebarOpen" class="ml-3 flex-1">Reports</span>

        <svg x-show="sidebarOpen"
             class="w-4 h-4 ml-2 transform transition-transform"
             :class="open ? 'rotate-180' : ''"
             fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                  d="M19 9l-7 7-7-7"/>
        </svg>
    </button>

    <!-- Submenu -->
    <div x-show="open" x-cloak class="ml-4 mt-1 space-y-1"
         x-transition.opacity.duration.200ms>
        <a href="{{ route('admin.reports.sales-detail') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-600 transition 
           {{ request()->routeIs('admin.reports.sales-detail') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
           Sales Detail Report
        </a>

        <a href="{{ route('admin.reports.event-analytics') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-600 transition
           {{ request()->routeIs('admin.reports.event-analytics') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
           Event Analytics
        </a>

        <a href="{{ route('admin.reports.top-events') }}" 
           class="block px-4 py-2 rounded hover:bg-gray-600 transition
           {{ request()->routeIs('admin.reports.top-events') ? 'bg-gray-700 text-white' : 'text-gray-300' }}">
           Top Events
        </a>
    </div>
</div>


                <!-- Other Menu -->
                <a href="{{ route('admin.events.index') }}"
                   class="flex items-center p-3 hover:bg-gray-700 rounded transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">Events</span>
                </a>

                <a href="{{ route('admin.banners.index') }}" class="flex items-center p-3 hover:bg-gray-700 rounded transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">Banners</span>
                </a>

                <a href="{{ route('admin.transactions.index') }}" 
                   class="flex items-center p-3 hover:bg-gray-700 rounded transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">Transactions</span>
                </a>

                <a href="{{ route('home') }}" 
                   class="flex items-center p-3 hover:bg-gray-700 rounded transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/>
                    </svg>
                    <span x-show="sidebarOpen" class="ml-3">View Site</span>
                </a>

            </nav>
        </div>
    </aside>

    <!-- Main -->
    <div class="flex-1 overflow-auto">
        <header class="bg-white shadow-sm">
            <div class="flex items-center justify-between p-4">
                <h2 class="text-xl font-semibold">@yield('title', 'Admin Panel')</h2>
                <div class="flex items-center gap-4">
                    <span class="text-gray-700">{{ auth()->user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-red-600 hover:text-red-700">Logout</button>
                    </form>
                </div>
            </div>
        </header>

        @if(session('success'))
            <div class="m-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="m-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                {{ session('error') }}
            </div>
        @endif

        <main class="p-8">
            @yield('content')
        </main>
    </div>
</div>
</body>
</html>
