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
        'code',
        'description',
    ];

    public function items()
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    public static function generateCode($name): string
    {
        $words = explode(' ', $name);
        if (count($words) === 1 && strlen($name) > 2) {
            return strtoupper(substr($name, 0, 2) . substr($name, -1));
        }
        
        $initials = array_map(function ($word) {
            return strtoupper(substr($word, 0, 1));
        }, $words);

        return implode('', $initials);
    }
} 