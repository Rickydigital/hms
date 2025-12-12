<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitLabOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'lab_test_id',
        'extra_instruction',
        'is_completed',
        'completed_at',
        'is_paid',        
        'paid_at',       
        'paid_by',        
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'is_paid'      => 'boolean',
            'completed_at' => 'datetime',
            'paid_at'      => 'datetime',
        ];
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function test()
    {
        return $this->belongsTo(LabTestMaster::class, 'lab_test_id');
    }

    public function result()
    {
        return $this->hasOne(LabResult::class, 'visit_lab_order_id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    // Helper methods
    public function markAsPaid($userId)
    {
        $this->update([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $userId,
        ]);
    }

    public function complete()
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }
}