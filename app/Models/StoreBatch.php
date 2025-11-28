<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreBatch extends Model {
    protected $fillable = ['item_id','batch_no','expiry_date','initial_quantity','current_stock','purchase_price','received_date','is_expired'];
    protected $casts = ['expiry_date'=>'date','received_date'=>'date','purchase_price'=>'decimal:2','is_expired'=>'boolean'];

    public function item() { return $this->belongsTo(StoreItemMaster::class, 'item_id'); }
    public function issues() { return $this->hasMany(StoreIssue::class); }

    public static function markExpired() {
        self::where('expiry_date', '<', today())->where('is_expired', false)->update(['is_expired' => true]);
    }
}