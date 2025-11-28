@extends('layouts.admin')

@section('title', 'Create Event')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Create New Event</h1>

    <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-lg shadow p-6">
        @csrf

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Title *</label>
            <input type="text" name="title" required
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                   value="{{ old('title') }}">
            @error('title')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Description *</label>
            <textarea name="description" rows="5" required
                      class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">{{ old('description') }}</textarea>
            @error('description')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-gray-700 font-semibold mb-2">Location *</label>
                <input type="text" name="location" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                       value="{{ old('location') }}">
                @error('location')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Event Date *</label>
                <input type="datetime-local" name="event_date" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                       value="{{ old('event_date') }}">
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
                       value="{{ old('price') }}">
                @error('price')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-gray-700 font-semibold mb-2">Quota *</label>
                <input type="number" name="quota" min="1" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600"
                       value="{{ old('quota') }}">
                @error('quota')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 font-semibold mb-2">Event Image</label>
            <input type="file" name="image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2">
            @error('image')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="mb-6">
            <label class="block text-gray-700 font-semibold mb-2">Status *</label>
            <select name="status" required
                    class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-600">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
                <option value="ended">Ended</option>
            </select>
            @error('status')
                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex gap-4">
            <button type="submit" 
                    class="flex-1 bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-bold">
                Create Event
            </button>
            <a href="{{ route('admin.events.index') }}" 
               class="flex-1 bg-gray-200 text-gray-700 py-3 rounded-lg hover:bg-gray-300 text-center font-bold">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection
