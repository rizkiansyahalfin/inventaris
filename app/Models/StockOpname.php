<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockOpname extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'status',
        'notes',
        'created_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    /**
     * Get the creator of the stock opname.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the stock opname items.
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    /**
     * Get the count of completed items.
     *
     * @return int
     */
    public function getCompletedItemsCountAttribute(): int
    {
        return $this->items()->whereNotNull('checked_at')->count();
    }

    /**
     * Get the total items count.
     *
     * @return int
     */
    public function getTotalItemsCountAttribute(): int
    {
        return $this->items()->count();
    }

    /**
     * Get the completion percentage.
     *
     * @return float
     */
    public function getCompletionPercentageAttribute(): float
    {
        $total = $this->total_items_count;
        if ($total === 0) {
            return 0;
        }

        return ($this->completed_items_count / $total) * 100;
    }

    /**
     * Get the progress percentage (alias for completion percentage).
     *
     * @return float
     */
    public function getProgressAttribute(): float
    {
        return $this->completion_percentage;
    }

    /**
     * Get the started_at date (alias for start_date).
     *
     * @return \Carbon\Carbon|null
     */
    public function getStartedAtAttribute()
    {
        return $this->start_date;
    }

    /**
     * Get the completed_at date (alias for end_date when status is completed).
     *
     * @return \Carbon\Carbon|null
     */
    public function getCompletedAtAttribute()
    {
        return $this->status === 'completed' ? $this->end_date : null;
    }

    /**
     * Get the code (alias for id).
     *
     * @return string
     */
    public function getCodeAttribute(): string
    {
        return 'SO-' . str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }
}
