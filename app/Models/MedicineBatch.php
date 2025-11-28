<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class MedicineBatch extends Model {
    use HasFactory;
    protected $fillable = [
        'medicine_id',
        'purchase_id',
        'batch_no',
        'expiry_date',
        'initial_quantity',
        'current_stock',
        'purchase_price',
        'manufacturing_date',
        'received_date',
        'is_expired'];
    protected $casts = ['expiry_date'=>'date','received_date'=>'date','manufacturing_date'=>'date','is_expired'=>'boolean','purchase_price'=>'decimal:2','selling_price'=>'decimal:2'];

   
    public function purchase() { return $this->belongsTo(MedicinePurchase::class); }

    public function medicine()
    {
        return $this->belongsTo(MedicineMaster::class, 'medicine_id');
        //                              ↑ foreign key column name
    }
    public function scopeAvailable($query)
{
    return $query->where('current_stock', '>', 0)
                 ->where('is_expired', false)
                 ->where('expiry_date', '>=', today());
}

// Auto mark expired daily (call from scheduler)
public static function markExpired()
{
    self::where('expiry_date', '<', today())
        ->where('is_expired', false)
        ->update(['is_expired' => true]);
}
   
}