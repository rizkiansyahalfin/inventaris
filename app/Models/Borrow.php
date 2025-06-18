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
        'approval_status',
        'approved_by',
        'approved_at',
        'rejection_reason',
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
        'approved_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(Attachment::class, 'attachable');
    }

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('approval_status', 'rejected');
    }

    public function scopeReturned($query)
    {
        return $query->where('status', 'returned');
    }

    public function scopeBorrowed($query)
    {
        return $query->where('status', 'borrowed');
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
        return $this->status === 'borrowed' && $this->approval_status === 'approved' && !$this->hasActiveExtensionRequest();
    }

    public function canSubmitFeedback(): bool
    {
        return $this->status === 'returned' && !$this->hasFeedback();
    }

    public function isPending(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function isApproved(): bool
    {
        return $this->approval_status === 'approved';
    }

    public function isRejected(): bool
    {
        return $this->approval_status === 'rejected';
    }

    public function canBeApproved(): bool
    {
        return $this->approval_status === 'pending';
    }

    public function canBeRejected(): bool
    {
        return $this->approval_status === 'pending';
    }
} 