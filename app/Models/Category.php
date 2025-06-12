<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class)
            ->withTimestamps();
    }

    public function getCodeAttribute(): string
    {
        $words = explode(' ', $this->name);
        if (count($words) === 1 && strlen($this->name) > 2) {
            // Handle single words like "Elektronik" -> "ELK" as per user example
             return strtoupper(substr($this->name, 0, 2) . substr($this->name, -1));
        }
        $initials = array_map(function ($word) {
            return strtoupper(substr($word, 0, 1));
        }, $words);

        return implode('', $initials);
    }
} 