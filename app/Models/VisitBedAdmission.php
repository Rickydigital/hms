<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VisitBedAdmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'ward_id',
        'admission_date',
        'discharge_date',
        'total_days',
        'bed_charges',
        'admission_reason',
        'doctor_instruction',
        'is_discharged',
        'discharged_at',
        'discharged_by',
    ];

    // THIS IS THE ONLY CORRECT WAY IN LARAVEL 11
    protected $casts = [
        'admission_date' => 'datetime:Y-m-d',     // ← Changed to datetime
        'discharge_date' => 'datetime:Y-m-d',     // ← Changed to datetime
        'discharged_at'  => 'datetime',
        'is_discharged'  => 'boolean',
        'bed_charges'    => 'decimal:2',
        'total_days'     => 'integer',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function ward()
    {
        return $this->belongsTo(Ward::class);
    }

    public function dischargedBy()
    {
        return $this->belongsTo(User::class, 'discharged_by');
    }

    // FINAL WORKING DISCHARGE METHOD
    public function discharge($userId)
    {
        $dischargeDate = Carbon::today();
        $days = $this->admission_date->diffInDays($dischargeDate) + 1; // ← NOW WORKS!
        $charges = $days * $this->ward->price_per_day;

        $this->update([
            'discharge_date' => $dischargeDate,
            'total_days'     => $days,
            'bed_charges'    => $charges,
            'is_discharged'  => true,
            'discharged_at'  => now(),
            'discharged_by'  => $userId,
        ]);

        // Return bed
        $this->ward->incrementAvailable();
    }

    protected static function booted()
    {
        static::created(function ($admission) {
            $admission->ward->decrementAvailable();
        });
    }
}