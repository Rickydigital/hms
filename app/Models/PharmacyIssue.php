<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacyIssue extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_medicine_order_id',
        'medicine_id',
        'batch_no',
        'expiry_date',
        'quantity_issued',
        'unit_price',
        'total_amount',
        'issued_by',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'issued_at'   => 'datetime',
            'unit_price'  => 'decimal:2',
            'total_amount'=> 'decimal:2',
        ];
    }

    public function order()
    {
        return $this->belongsTo(VisitMedicineOrder::class, 'visit_medicine_order_id');
    }

    public function medicine()
    {
        return $this->belongsTo(MedicineMaster::class);
    }

    public function issuedBy()
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    // Auto mark parent order as issued + reduce stock when this record is created
    protected static function booted()
    {
        static::created(function ($issue) {
            // Mark the prescription as issued
            $issue->order->markAsIssued($issue->issued_by);

            // Reduce stock from batch table (we'll create stock table later)
            // MedicineBatch::where('medicine_id', $issue->medicine_id)
            //              ->where('batch_no', $issue->batch_no)
            //              ->decrement('current_stock', $issue->quantity_issued);
        });
    }
}