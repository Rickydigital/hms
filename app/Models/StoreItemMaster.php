<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StoreItemMaster extends Model {
    use HasFactory;
    protected $fillable = ['item_code','item_name','unit','price','minimum_stock','is_active'];
    protected $casts = ['price'=>'decimal:2'];

    protected static function booted() {
        static::creating(function ($item) {
            if (empty($item->item_code)) {
                $latest = self::latest('id')->first();
                $number = $latest ? (int)substr($latest->item_code, 3) + 1 : 1;
                $item->item_code = 'STR' . str_pad($number, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    public function batches() { return $this->hasMany(StoreBatch::class, 'item_id'); }
}