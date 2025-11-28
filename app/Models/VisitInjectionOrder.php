<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitInjectionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'visit_id',
        'medicine_id',
        'route',
        'instruction',
        'is_given',
        'given_at',
        'given_by',
    ];

    protected function casts(): array
    {
        return [
            'is_given' => 'boolean',
            'given_at' => 'datetime',
        ];
    }

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function medicine()
    {
        return $this->belongsTo(MedicineMaster::class);
    }

    public function givenBy()
    {
        return $this->belongsTo(User::class, 'given_by');
    }

    public function markAsGiven($userId)
    {
        $this->update([
            'is_given' => true,
            'given_at' => now(),
            'given_by' => $userId,
        ]);
    }
};