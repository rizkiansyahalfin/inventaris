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
        'stock',
        'condition',
        'status',
        'location',
        'purchase_price',
        'purchase_date',
        'warranty_expiry',
        'supplier',
        'notes'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
        'warranty_expiry' => 'date',
        'stock' => 'integer',
    ];

    const STATUS_AVAILABLE = 'Tersedia';
    const STATUS_BORROWED = 'Dipinjam';
    const STATUS_MAINTENANCE = 'Dalam Perbaikan';
    const STATUS_DAMAGED = 'Rusak';
    const STATUS_LOST = 'Hilang';

    public static function getStatuses()
    {
        return [
            self::STATUS_AVAILABLE,
            self::STATUS_BORROWED,
            self::STATUS_MAINTENANCE,
            self::STATUS_DAMAGED,
            self::STATUS_LOST
        ];
    }

    public function generateUnitCodes()
    {
        if ($this->stock <= 1) {
            return [$this->code];
        }

        $codes = [];
        $baseCode = $this->code;
        
        // Generate kode unik untuk setiap unit
        for ($i = 1; $i <= $this->stock; $i++) {
            $codes[] = $baseCode . '-' . str_pad($i, 3, '0', STR_PAD_LEFT);
        }

        return $codes;
    }

    public function updateStatus($newStatus)
    {
        $this->status = $newStatus;
        $this->save();
        
        return $this;
    }

    public function updateCondition($newCondition)
    {
        $this->condition = $newCondition;
        
        // Perbarui status berdasarkan kondisi
        $this->updateStatusFromCondition();
        
        $this->save();
        
        return $this;
    }
    
    public function updateStatusFromCondition()
    {
        // Hanya ubah status jika barang tidak sedang dipinjam
        if ($this->status !== self::STATUS_BORROWED) {
            if ($this->condition === 'Rusak Berat') {
                $this->status = self::STATUS_DAMAGED;
            } elseif ($this->condition === 'Rusak Ringan') {
                $this->status = self::STATUS_MAINTENANCE;
            } else {
                // Jika kondisi baik, kembalikan ke status tersedia
                $this->status = self::STATUS_AVAILABLE;
            }
        }
        
        return $this;
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
        return $this->hasMany(Borrow::class)->orderBy('borrow_date', 'desc');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class)->orderBy('start_date', 'desc');
    }
} 