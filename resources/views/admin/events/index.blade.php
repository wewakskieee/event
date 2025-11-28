@extends('layouts.admin')

@section('title', 'Manage Events')

@section('content')
<div class="mb-6 flex justify-between items-center">
    <h1 class="text-2xl font-bold">Events Management</h1>
    <a href="{{ route('admin.events.create') }}" 
       class="bg-indigo-600 text-white px-6 py-2 rounded-lg hover:bg-indigo-700">
        + Create Event
    </a>
</div>

<div class="bg-white rounded-lg shadow overflow-hidden">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Title</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quota</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($events as $event)
            <tr>
                <td class="px-6 py-4">
                    <img src="{{ $event->image ? Storage::url($event->image) : 'https://via.placeholder.com/80x60' }}" 
                         alt="{{ $event->title }}" 
                         class="w-20 h-15 object-cover rounded">
                </td>
                <td class="px-6 py-4">
                    <div class="font-semibold">{{ $event->title }}</div>
                    <div class="text-sm text-gray-500">{{ $event->location }}</div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ $event->event_date->format('d M Y') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    Rp {{ number_format($event->price, 0, ',', '.') }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    {{ $event->quota_remaining }} / {{ $event->quota }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full
                        {{ $event->status === 'published' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $event->status === 'draft' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $event->status === 'ended' ? 'bg-gray-100 text-gray-800' : '' }}">
                        {{ ucfirst($event->status) }}
                    </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="{{ route('admin.events.edit', $event->id) }}" 
                       class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                    <form action="{{ route('admin.events.destroy', $event->id) }}" 
                          method="POST" 
                          class="inline"
                          onsubmit="return confirm('Are you sure?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:text-red-900">Delete</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
