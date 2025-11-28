<?php
// app/Models/Event.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'image',
        'location',
        'price',
        'quota',
        'quota_remaining',
        'event_date',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'event_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($event) {
            if (empty($event->slug)) {
                $event->slug = Str::slug($event->title);
            }
            if (is_null($event->quota_remaining)) {
                $event->quota_remaining = $event->quota;
            }
        });
    }

    public function banners()
    {
        return $this->hasMany(EventBanner::class)->orderBy('order');
    }

    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function logs()
    {
        return $this->hasMany(LogEvent::class);
    }

    public function isAlmostSoldOut(): bool
    {
        return $this->quota_remaining <= ($this->quota * 0.1);
    }

    public function isSoldOut(): bool
    {
        return $this->quota_remaining <= 0;
    }
}
