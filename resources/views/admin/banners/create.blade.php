@extends('layouts.admin')

@section('title', 'Upload Banner')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Upload New Banner</h1>
        <p class="text-gray-600">Add banner for event slider</p>
    </div>

    <form action="{{ route('admin.banners.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Select Event *</label>
            <select name="event_id" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="">-- Choose Event --</option>
                @foreach($events as $event)
                    <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                        {{ $event->title }}
                    </option>
                @endforeach
            </select>
            @error('event_id')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Banner Image *</label>
            <input type="file" name="image" accept="image/*" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
            <p class="text-xs text-gray-500 mt-1">Recommended size: 1200x400px</p>
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Order</label>
                <input type="number" name="order" min="0" value="{{ old('order', 0) }}"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                @error('order')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Status</label>
                <select name="is_active"
                        class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                    <option value="1" {{ old('is_active', 1) == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ old('is_active') == 0 ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('is_active')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="flex gap-4">
            <button type="submit" 
                    class="flex-1 bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-bold transition">
                Upload Banner
            </button>
            <a href="{{ route('admin.banners.index') }}" 
               class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 text-center font-bold transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
