<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'supplier_code',
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Auto generate supplier code: SU0001, SU0002...
    protected static function booted()
    {
        static::creating(function ($supplier) {
            if (empty($supplier->supplier_code)) {
                $latest = self::latest('id')->first();
                $number = $latest ? (int)substr($latest->supplier_code, 2) + 1 : 1;
                $supplier->supplier_code = 'SU' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function purchases()
    {
        return $this->hasMany(MedicinePurchase::class);
    }
}