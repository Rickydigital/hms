<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'visit_date',
        'visit_time',
        'status',
        'registration_amount',
        'registration_paid',
        'all_services_completed',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
            'visit_time' => 'datetime:H:i',
            'registration_paid' => 'boolean',
            'all_services_completed' => 'boolean',
            'registration_amount' => 'decimal:2',
        ];
    }

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function vitals()
    {
        return $this->hasOne(PatientVital::class);
    }

    public function labOrders()
    {
        return $this->hasMany(VisitLabOrder::class);
    }

    public function medicineOrders()
    {
        return $this->hasMany(VisitMedicineOrder::class);
    }

    public function injectionOrders()           // ADD THIS
    {
        return $this->hasMany(VisitInjectionOrder::class);
    }

    public function bedAdmission()              // ADD THIS
    {
        return $this->hasOne(VisitBedAdmission::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function receipt()
{
    return $this->hasOne(Receipt::class, 'visit_id');
}

public function medicineIssues()
{
    return $this->hasManyThrough(
        PharmacyIssue::class,
        VisitMedicineOrder::class,
        'visit_id',
        'visit_medicine_order_id',
        'id',
        'id'
    );
}

}