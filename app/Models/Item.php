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
        'description',
        'condition',
        'location',
        'purchase_price',
        'purchase_date',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($item) {
            $initial = strtoupper(substr($item->name, 0, 1));
            $lastItem = self::where('code', 'like', $initial . '%')->orderBy('code', 'desc')->first();
            $number = 1;
            if ($lastItem) {
                $number = (int)substr($lastItem->code, 1) + 1;
            }
            $item->code = $initial . str_pad($number, 3, '0', STR_PAD_LEFT);
        });
    }

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
        return $this->hasMany(Borrow::class);
    }
} 