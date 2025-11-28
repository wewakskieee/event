<?php
// app/Models/LogTransaction.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTransaction extends Model
{
    use HasFactory;

    protected $table = 'logs_transaction';

    protected $fillable = [
        'transaction_id',
        'action',
        'old_status',
        'new_status',
    ];

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }
}
