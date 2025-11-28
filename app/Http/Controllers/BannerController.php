<?php
// app/Http/Controllers/BannerController.php

namespace App\Http\Controllers;

use App\Models\EventBanner;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        $banners = EventBanner::with('event')->latest()->get();
        
        return view('admin.banners.index', compact('banners'));
    }

    public function create()
    {
        $events = Event::all();
        
        return view('admin.banners.create', compact('events'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'event_id' => 'required|exists:events,id',
            'image' => 'required|image|max:2048',
            'order' => 'nullable|integer',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('banners', 'public');
        }

        EventBanner::create($validated);

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner created successfully');
    }

    public function destroy($id)
    {
        $banner = EventBanner::findOrFail($id);
        
        if ($banner->image) {
            Storage::disk('public')->delete($banner->image);
        }

        $banner->delete();

        return redirect()->route('admin.banners.index')
            ->with('success', 'Banner deleted successfully');
    }
}
