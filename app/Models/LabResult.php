<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LabResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_lab_order_id',
        'result_value',
        'result_text',
        'remarks',
        'normal_range',
        'is_abnormal',
        'technician_id',
        'reported_at',
    ];

    protected function casts(): array
    {
        return [
            'is_abnormal' => 'boolean',
            'reported_at' => 'datetime',
        ];
    }

    public function order()
    {
        return $this->belongsTo(VisitLabOrder::class, 'visit_lab_order_id');
    }

    public function technician()
    {
        return $this->belongsTo(User::class, 'technician_id');
    }

    

    // Auto-complete the parent order when result is saved
    protected static function booted()
    {
        static::created(function ($result) {
            $result->order->update([
                'is_completed' => true,
                'completed_at' => now(),
            ]);
        });
    }
}