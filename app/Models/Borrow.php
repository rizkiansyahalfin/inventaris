<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrow extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'user_id',
        'item_id',
        'quantity',
        'status',
        'borrow_date',
        'due_date',
        'return_date',
        'condition_at_borrow',
        'condition_on_return',
        'notes',
    ];

    protected $casts = [
        'borrow_date' => 'date',
        'due_date' => 'date',
        'return_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function extensions(): HasMany
    {
        return $this->hasMany(BorrowExtension::class);
    }

    public function feedback(): HasOne
    {
        return $this->hasOne(ItemFeedback::class);
    }

    public function hasFeedback(): bool
    {
        return $this->feedback()->exists();
    }

    public function hasActiveExtensionRequest(): bool
    {
        return $this->extensions()->pending()->exists();
    }

    public function canBeExtended(): bool
    {
        return $this->status === 'approved' && !$this->hasActiveExtensionRequest();
    }

    public function canSubmitFeedback(): bool
    {
        return $this->status === 'returned' && !$this->hasFeedback();
    }
} 