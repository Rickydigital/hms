<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitProcedure extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'procedure_id',
        'is_issued',
        'issued_at',
        'is_paid',
        'paid_at',
        'paid_by',
    ];

    protected function casts(): array
    {
        return [
            'is_issued' => 'boolean',
            'is_paid'   => 'boolean',
            'issued_at' => 'datetime',
            'paid_at'   => 'datetime',
        ];
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function procedure()
    {
        return $this->belongsTo(ProcedureMaster::class, 'procedure_id');
    }

    public function paidBy()
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function markAsPaid($userId)
    {
        $this->update([
            'is_paid' => true,
            'paid_at' => now(),
            'paid_by' => $userId,
        ]);
    }

    public function markAsIssued()
    {
        $this->update([
            'is_issued' => true,
            'issued_at' => now(),
        ]);
    }
}