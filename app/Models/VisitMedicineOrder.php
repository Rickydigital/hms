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
        'is_paid',         
        'paid_at',         
        'paid_by',          
        'handed_over_at',    
        'handed_over_by',    
    ];

    protected function casts(): array
    {
        return [
            'is_issued'      => 'boolean',
            'is_paid'        => 'boolean',
            'issued_at'      => 'datetime',
            'paid_at'        => 'datetime',
            'handed_over_at' => 'datetime',
            'duration_days'  => 'integer',
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

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function handedOverBy()
    {
        return $this->belongsTo(User::class, 'handed_over_by');
    }

    // Helper methods
    public function markAsIssued($userId)
    {
        $this->update([
            'is_issued' => true,
            'issued_at' => now(),
            'issued_by' => $userId,
        ]);
    }

    public function markAsPaid($userId)
    {
        $this->update([
            'is_paid'   => true,
            'paid_at'   => now(),
            'paid_by'   => $userId,
        ]);
    }

    public function markAsHandedOver($userId)
    {
        $this->update([
            'handed_over_at' => now(),
            'handed_over_by' => $userId,
        ]);
    }


    public function pharmacyIssues()
    {
        return $this->hasMany(PharmacyIssue::class, 'visit_medicine_order_id');
    }
}