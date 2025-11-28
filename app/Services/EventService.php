<?php
// app/Services/EventService.php

namespace App\Services;

use App\Repositories\Contracts\EventRepositoryInterface;
use Illuminate\Support\Facades\Storage;

class EventService
{
    protected $eventRepository;

    public function __construct(EventRepositoryInterface $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function getAllEvents()
    {
        return $this->eventRepository->all();
    }

    public function getEventById($id)
    {
        return $this->eventRepository->find($id);
    }

    public function getEventBySlug($slug)
    {
        return $this->eventRepository->findBySlug($slug);
    }

    public function getPublishedEvents()
    {
        return $this->eventRepository->getPublishedEvents();
    }

    public function getFeaturedEvents($limit = 6)
    {
        return $this->eventRepository->getFeaturedEvents($limit);
    }

    public function createEvent(array $data)
    {
        if (isset($data['image'])) {
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->eventRepository->create($data);
    }

    public function updateEvent($id, array $data)
    {
        $event = $this->eventRepository->find($id);

        if (isset($data['image'])) {
            // Delete old image
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $this->uploadImage($data['image']);
        }

        return $this->eventRepository->update($id, $data);
    }

    public function deleteEvent($id)
    {
        $event = $this->eventRepository->find($id);
        
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        return $this->eventRepository->delete($id);
    }

    protected function uploadImage($image)
    {
        return $image->store('events', 'public');
    }

    public function getStats()
    {
        $events = $this->eventRepository->all();
        
        return [
            'total_events' => $events->count(),
            'published_events' => $events->where('status', 'published')->count(),
            'total_quota' => $events->sum('quota'),
            'sold_tickets' => $events->sum(function($event) {
                return $event->quota - $event->quota_remaining;
            })
        ];
    }
}
