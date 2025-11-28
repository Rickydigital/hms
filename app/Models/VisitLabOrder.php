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
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'completed_at' => 'datetime',
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

    public function complete()
    {
        $this->update([
            'is_completed' => true,
            'completed_at' => now(),
        ]);
    }
}