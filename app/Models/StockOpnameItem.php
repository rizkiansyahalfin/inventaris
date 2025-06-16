<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockOpnameItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_opname_id',
        'item_id',
        'expected_quantity',
        'actual_quantity',
        'condition',
        'notes',
        'checked_by',
        'checked_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'checked_at' => 'datetime',
    ];

    /**
     * Get the stock opname.
     */
    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(StockOpname::class);
    }

    /**
     * Get the item.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Get the user who checked the item.
     */
    public function checkedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    /**
     * Check if there is a quantity discrepancy.
     *
     * @return bool
     */
    public function hasDiscrepancy(): bool
    {
        return $this->expected_quantity !== $this->actual_quantity;
    }

    /**
     * Get the quantity difference.
     *
     * @return int
     */
    public function getQuantityDifferenceAttribute(): int
    {
        return $this->actual_quantity - $this->expected_quantity;
    }
}
