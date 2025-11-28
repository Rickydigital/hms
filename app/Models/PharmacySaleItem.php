<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PharmacySaleItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_sale_id', 'medicine_id', 'batch_id', 'batch_no',
        'expiry_date', 'quantity', 'unit_price', 'total_price'
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(PharmacySale::class);
    }

    public function medicine()
    {
        return $this->belongsTo(MedicineMaster::class);
    }

    public function batch()
    {
        return $this->belongsTo(MedicineBatch::class);
    }
}