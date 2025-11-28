<?php
// app/Http/Controllers/EventController.php

namespace App\Http\Controllers;

use App\Services\EventService;
use App\Models\Event;
use App\Models\EventBanner;
use Illuminate\Http\Request;

class EventController extends Controller
{
    protected $eventService;

    public function __construct(EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function index()
    {
        // -------------------------
        // 🔥 Logic tambahan dari kode pertama
        // -------------------------

        $banners = EventBanner::with('event')
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
        
        $events = Event::with('banners')
            ->where('status', 'published')
            ->where('event_date', '>', now())
            ->orderBy('event_date')
            ->paginate(12);
        
        // Event terdekat untuk countdown
        $upcomingEvent = Event::where('status', 'published')
            ->where('event_date', '>', now())
            ->orderBy('event_date', 'asc')
            ->first();
        
        // Event trending (paling banyak tiket terjual)
        $trendingEvents = Event::with('banners')
            ->where('status', 'published')
            ->where('event_date', '>', now())
            ->withCount(['tickets' => function($query) {
                $query->whereHas('transaction', function($q) {
                    $q->where('status', 'paid');
                });
            }])
            ->orderBy('tickets_count', 'desc')
            ->limit(3)
            ->get();

        // -------------------------
        // 🎯 Logic original untuk service-layer system
        // -------------------------

        $featuredEvents = $this->eventService->getFeaturedEvents(6);

        return view('home', compact(
            'banners', 
            'events', 
            'featuredEvents', 
            'upcomingEvent', 
            'trendingEvents'
        ));
    }

    public function show($slug)
    {
        $event = $this->eventService->getEventBySlug($slug);
        
        return view('event.detail', compact('event'));
    }

    public function adminIndex()
    {
        $events = $this->eventService->getAllEvents();
        
        return view('admin.events.index', compact('events'));
    }

    public function create()
    {
        return view('admin.events.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'event_date' => 'required|date|after:now',
            'status' => 'required|in:draft,published,ended',
        ]);

        $this->eventService->createEvent($validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event created successfully');
    }

    public function edit($id)
    {
        $event = $this->eventService->getEventById($id);
        
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|max:2048',
            'location' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'quota' => 'required|integer|min:1',
            'event_date' => 'required|date',
            'status' => 'required|in:draft,published,ended',
        ]);

        $this->eventService->updateEvent($id, $validated);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event updated successfully');
    }

    public function destroy($id)
    {
        $this->eventService->deleteEvent($id);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event deleted successfully');
    }
}
