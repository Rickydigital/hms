<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicineStockLog extends Model
{
    protected $guarded = ['id'];

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