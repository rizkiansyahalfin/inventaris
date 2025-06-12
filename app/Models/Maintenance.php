<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'user_id',
        'type',
        'title',
        'notes',
        'cost',
        'start_date',
        'completion_date',
    ];

    protected $casts = [
        'start_date' => 'date',
        'completion_date' => 'date',
        'cost' => 'decimal:2',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
