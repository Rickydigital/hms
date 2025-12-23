<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MedicineMaster extends Model
{
    use HasFactory;
protected $table = 'medicines_master';
    protected $fillable = [
        'medicine_code',
        'medicine_name',
        'generic_name',
        'packing',
        'type',
        'price',
        'purchase_price',
        'is_injectable',
        'is_active',
        'minimum_stock',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'price'           => 'decimal:2',
            'purchase_price'  => 'decimal:2',
            'is_injectable'   => 'boolean',
            'is_active'       => 'boolean',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }


public function batches()
{
    return $this->hasMany(MedicineBatch::class, 'medicine_id');
    //                                          ↑ foreign key
}

public function availableBatches()
{
    return $this->hasMany(MedicineBatch::class, 'medicine_id')  // ← foreign key
                ->where('current_stock', '>', 0)
                ->where('is_expired', false)
                ->where('expiry_date', '>=', today());
}

public function totalStock()
{
    return $this->batches()->sum('current_stock');
}

    public function scopeInjectable($query)
    {
        return $query->where('is_injectable', true);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }


    // Add this method to get total available stock
public function currentStock()
{
    return $this->batches()
                ->where('current_stock', '>', 0)
                ->where('is_expired', false)
                ->where('expiry_date', '>=', today())
                ->sum('current_stock');
}

// Optional: profit margin
public function profitMargin($batchPurchasePrice)
{
    return $this->selling_price - $batchPurchasePrice;
}

public function scopeInStock($query)
{
    return $query->withSum('batches as current_stock', 'current_stock')
                 ->having('current_stock', '>', 0);
}
}