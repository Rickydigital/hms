<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class PaymentDetail extends Model {
    protected $fillable = ['payment_id','item_type','item_name','quantity','unit_price','total_price'];
    protected $casts = ['unit_price'=>'decimal:2', 'total_price'=>'decimal:2'];

    public function payment() { return $this->belongsTo(Payment::class); }
}