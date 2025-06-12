<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Item extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'code',
        'qr_code',
        'image',
        'description',
        'condition',
        'status',
        'location',
        'purchase_price',
        'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class)
            ->withTimestamps();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function borrows(): HasMany
    {
        return $this->hasMany(Borrow::class)->orderBy('borrow_date', 'desc');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class)->orderBy('start_date', 'desc');
    }
} 