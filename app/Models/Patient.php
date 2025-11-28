<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'name',
        'age',
        'gender',
        'phone',
        'address',
        'registration_date',
        'expiry_date',
        'is_active',
        'reactivation_fee_paid',
        'total_visits',
    ];

    // THIS IS THE ONLY CORRECT WAY IN LARAVEL 11
    protected $casts = [
        'registration_date'     => 'datetime:Y-m-d',
        'expiry_date'           => 'datetime:Y-m-d',
        'is_active'             => 'boolean',
        'reactivation_fee_paid' => 'string',   // ← CHANGED FROM 'decimal:2' TO 'string'
        'total_visits'          => 'integer',
    ];

    // Auto generate Patient ID
    public static function generatePatientId(): string
    {
        $prefix = 'CWH' . date('Y');
        $last = self::where('patient_id', 'like', $prefix . '%')
                     ->orderByDesc('id')
                     ->first();

        $number = $last ? (int)substr($last->patient_id, -6) + 1 : 1;
        return $prefix . '-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }

    public function isExpired(): bool
    {
        return $this->expiry_date < Carbon::today();
    }

    // FINAL WORKING REACTIVATE METHOD
    public function reactivate(int $months = 12, float $fee = 5000.00): void
    {
        $this->expiry_date = now()->addMonths($months);
        $this->is_active = true;
        $this->reactivation_fee_paid = number_format($fee, 2, '.', ''); // ← Convert to string
        $this->save();
    }

protected static function booted()
{
    static::creating(function ($patient) {
        if (empty($patient->patient_id)) {
            $patient->patient_id = self::generatePatientId();
        }
        if (empty($patient->registration_date)) {
            $patient->registration_date = now();
        }
        if (empty($patient->expiry_date)) {
            $patient->expiry_date = now()->addMonths(
                (int) \App\Models\Setting::get('card_validity_months', 12)
            );
        }
        $patient->is_active = true;
    });
}

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    // Optional: Access as float in code
    public function getReactivationFeePaidAttribute($value)
    {
        return $value ? (float) $value : 0.00;
    }
}