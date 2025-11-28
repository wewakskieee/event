@extends('layouts.admin')

@section('title', 'Edit Event')

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="mb-6">
        <h1 class="text-2xl font-bold">Edit Event</h1>
        <p class="text-gray-600">Update event information</p>
    </div>

    <form action="{{ route('admin.events.update', $event->id) }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
        @csrf
        @method('PUT')

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Title *</label>
            <input type="text" name="title" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                   value="{{ old('title', $event->title) }}">
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Description *</label>
            <textarea name="description" rows="5" required
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">{{ old('description', $event->description) }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Location *</label>
                <input type="text" name="location" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                       value="{{ old('location', $event->location) }}">
                @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Event Date *</label>
                <input type="datetime-local" name="event_date" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                       value="{{ old('event_date', $event->event_date->format('Y-m-d\TH:i')) }}">
                @error('event_date')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Price (Rp) *</label>
                <input type="number" name="price" min="0" step="1000" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                       value="{{ old('price', $event->price) }}">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Quota *</label>
                <input type="number" name="quota" min="1" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                       value="{{ old('quota', $event->quota) }}">
                @error('quota')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-xs text-gray-500 mt-1">Current remaining: {{ $event->quota_remaining }}</p>
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Image</label>
            @if($event->image)
                <div class="mb-3">
                    <img src="{{ Storage::url($event->image) }}" alt="Current Image" class="w-48 h-32 object-cover rounded-lg">
                    <p class="text-sm text-gray-500 mt-1">Current image</p>
                </div>
            @endif
            <input type="file" name="image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
            <p class="text-xs text-gray-500 mt-1">Leave empty to keep current image</p>
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Status *</label>
            <select name="status" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="draft" {{ old('status', $event->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $event->status) == 'published' ? 'selected' : '' }}>Published</option>
                <option value="ended" {{ old('status', $event->status) == 'ended' ? 'selected' : '' }}>Ended</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" 
                    class="flex-1 bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-bold transition">
                Update Event
            </button>
            <a href="{{ route('admin.events.index') }}" 
               class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 text-center font-bold transition">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
