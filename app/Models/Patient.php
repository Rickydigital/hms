<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Patient extends Model
{
    use HasFactory;

// App\Models\Patient.php

protected $fillable = [
    'patient_id',
    'name',
    'age',
    'age_months',     // ← added
    'age_days',       // ← added
    'gender',
    'phone',
    'address',
    'registration_date',
    'expiry_date',
    'is_active',
    'reactivation_fee_paid',
    'total_visits',
];

protected $casts = [
    'registration_date'     => 'datetime:Y-m-d',
    'expiry_date'           => 'datetime:Y-m-d',
    'is_active'             => 'boolean',
    'reactivation_fee_paid' => 'string',
    'total_visits'          => 'integer',
    'age_months'            => 'integer',
    'age_days'              => 'integer',
];

// Smart age display
public function getAgeDisplayAttribute(): string
{
    if ($this->age_months !== null || $this->age_days !== null) {
        $parts = [];
        if ($this->age_months !== null) {
            $parts[] = $this->age_months . ' month' . ($this->age_months != 1 ? 's' : '');
        }
        if ($this->age_days !== null) {
            $parts[] = $this->age_days . ' day' . ($this->age_days != 1 ? 's' : '');
        }
        return implode(' ', $parts) ?: 'Newborn';
    }

    return $this->age . ' yr' . ($this->age != 1 ? 's' : '');
}

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