<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineStockLog extends Model
{
    protected $guarded = ['id'];

    // app/Models/MedicineStockLog.php

        protected $fillable = [
            'medicine_id',
            'batch_id',
            'quantity',
            'stock_before',   // ← add
            'stock_after',    // ← add
            'type',
            'reference_type',
            'reference_id',
            'remarks',
            'created_by',
        ];

    public function medicine()
    {
        return $this->belongsTo(MedicineMaster::class);
    }

    public function batch()
    {
        return $this->belongsTo(MedicineBatch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }
}