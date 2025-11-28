<?php
// app/Repositories/EventRepository.php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Contracts\EventRepositoryInterface;

class EventRepository implements EventRepositoryInterface
{
    protected $model;

    public function __construct(Event $event)
    {
        $this->model = $event;
    }

    public function all()
    {
        return $this->model->with('banners')->latest()->get();
    }

    public function find($id)
    {
        return $this->model->with('banners')->findOrFail($id);
    }

    public function findBySlug($slug)
    {
        return $this->model->with('banners')->where('slug', $slug)->firstOrFail();
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $event = $this->find($id);
        $event->update($data);
        return $event;
    }

    public function delete($id)
    {
        $event = $this->find($id);
        return $event->delete();
    }

    public function getPublishedEvents()
    {
        return $this->model
            ->with('banners')
            ->where('status', 'published')
            ->where('event_date', '>', now())
            ->orderBy('event_date')
            ->get();
    }

    public function getFeaturedEvents($limit = 6)
    {
        return $this->model
            ->with('banners')
            ->where('status', 'published')
            ->where('event_date', '>', now())
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
