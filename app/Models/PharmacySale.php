<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacySale extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_no', 'customer_name', 'customer_phone',
        'total_amount', 'amount_paid', 'change_due',
        'remarks', 'sold_by', 'sold_at'
    ];

    protected $casts = [
        'sold_at' => 'datetime',
        'total_amount' => 'decimal:2',
        'amount_paid' => 'decimal:2',
        'change_due' => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(PharmacySaleItem::class);
    }

    public function soldBy()
    {
        return $this->belongsTo(User::class, 'sold_by');
    }

    public static function generateInvoiceNo()
    {
        $today = today()->format('Ymd');
        $count = self::whereDate('sold_at', today())->count() + 1;
        return "OTC-{$today}-" . str_pad($count, 4, '0', STR_PAD_LEFT);
    }
}