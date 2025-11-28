<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PatientVital extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'height',
        'weight',
        'bp',
        'temperature',
        'pulse',
        'respiration',
        'chief_complaint',
        'history',
    ];

    protected function casts(): array
    {
        return [
            'height'       => 'decimal:2',
            'weight'       => 'decimal:2',
            'temperature'  => 'decimal:1',
        ];
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}