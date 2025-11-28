<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MedicinePurchase extends Model {
    use HasFactory;
    protected $fillable = ['invoice_no','invoice_date','supplier_id','total_amount','discount','net_amount','remarks','received_by','received_at'];
    protected $casts = ['invoice_date'=>'date', 'received_at'=>'datetime', 'total_amount'=>'decimal:2', 'discount'=>'decimal:2', 'net_amount'=>'decimal:2'];

    public function supplier() { return $this->belongsTo(Supplier::class); }
    public function receivedBy() { return $this->belongsTo(User::class, 'received_by'); }
    public function batches()
{
    return $this->hasMany(MedicineBatch::class, 'purchase_id');
}

    protected static function booted() {
        static::saving(fn($p) => $p->net_amount = $p->total_amount - $p->discount);
    }
}