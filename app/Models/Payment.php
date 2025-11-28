<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payment extends Model {
    use HasFactory;
    protected $fillable = ['visit_id','amount','type','payment_method','transaction_id','received_by','paid_at'];
    protected $casts = ['amount'=>'decimal:2', 'paid_at'=>'datetime'];

    public function visit() { return $this->belongsTo(Visit::class); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }
    public function details() { return $this->hasMany(PaymentDetail::class); }

    
}