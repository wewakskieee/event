<?php
// app/Models/LogEvent.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogEvent extends Model
{
    use HasFactory;

    protected $table = 'logs_event';

    protected $fillable = [
        'event_id',
        'action',
        'old_data',
        'new_data',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
