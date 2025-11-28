@extends('layouts.admin')

@section('title', 'Manage Banners')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold">Banners Management</h1>
        <p class="text-gray-600">Manage event banners for slider</p>
    </div>
    <a href="{{ route('admin.banners.create') }}" 
       class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
        + Upload Banner
    </a>
</div>

@if($banners->count() > 0)
<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preview</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Event</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($banners as $banner)
            <tr>
                <td class="px-6 py-4 whitespace-nowrap">
                    <img src="{{ Storage::url($banner->image) }}" 
                         alt="Banner" 
                         class="w-32 h-20 object-cover rounded">
                </td>
                <td class="px-6 py-4">
                    <div class="font-semibold">{{ $banner->event->title }}</div>
                    <div class="text-sm text-gray-500">{{ $banner->event->event_date->format('d M Y') }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-sm">
                        {{ $banner->order }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    @if($banner->is_active)
                        <span class="px-3 py-1 bg-green-100 text-green-800 rounded-full text-xs font-semibold">
                            Active
                        </span>
                    @else
                        <span class="px-3 py-1 bg-gray-100 text-gray-800 rounded-full text-xs font-semibold">
                            Inactive
                        </span>
                    @endif
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    {{ $banner->created_at->format('d M Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <form action="{{ route('admin.banners.destroy', $banner->id) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Are you sure you want to delete this banner?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900 font-semibold">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@else
<div class="bg-white rounded-lg shadow p-12 text-center">
    <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
    </svg>
    <h3 class="text-lg font-semibold text-gray-700 mb-2">No Banners Yet</h3>
    <p class="text-gray-500 mb-4">Upload your first banner to get started</p>
    <a href="{{ route('admin.banners.create') }}" 
       class="inline-block bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700 transition">
        Upload Banner
    </a>
</div>
@endif

<!-- Preview Section -->
@if($banners->where('is_active', true)->count() > 0)
<div class="mt-8">
    <h2 class="text-xl font-bold mb-4">Active Banners Preview</h2>
    <div class="bg-white rounded-lg shadow p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($banners->where('is_active', true)->sortBy('order') as $banner)
            <div class="border-2 border-gray-200 rounded-lg overflow-hidden">
                <img src="{{ Storage::url($banner->image) }}" 
                     alt="{{ $banner->event->title }}" 
                     class="w-full h-40 object-cover">
                <div class="p-3 bg-gray-50">
                    <p class="font-semibold text-sm">{{ $banner->event->title }}</p>
                    <p class="text-xs text-gray-500">Order: {{ $banner->order }}</p>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
