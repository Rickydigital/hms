<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ward extends Model
{
    use HasFactory;

    protected $fillable = [
        'ward_code',
        'ward_name',
        'price_per_day',
        'total_beds',
        'available_beds',
        'is_active',
        'facilities',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price_per_day' => 'decimal:2',
            'is_active'     => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Auto update available beds when patient admitted/discharged
    public function decrementAvailable()
    {
        $this->decrement('available_beds');
    }

    public function incrementAvailable()
    {
        $this->increment('available_beds');
    }
}