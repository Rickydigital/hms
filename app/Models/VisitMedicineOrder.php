<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitMedicineOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'medicine_id',
        'dosage',
        'duration_days',
        'instruction',
        'is_issued',
        'issued_at',
        'issued_by',
    ];

    protected function casts(): array
    {
        return [
            'is_issued' => 'boolean',
            'issued_at' => 'datetime',
            'duration_days' => 'integer',
        ];
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function medicine()
    {
        return $this->belongsTo(MedicineMaster::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // Mark as issued (called by Pharmacist)
    public function markAsIssued($userId)
    {
        $this->update([
            'is_issued' => true,
            'issued_at' => now(),
            'issued_by' => $userId,
        ]);
    }
}