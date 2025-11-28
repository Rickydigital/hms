<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class StoreIssue extends Model {
    protected $fillable = ['store_batch_id','quantity_issued','issued_to','requested_by','purpose','issued_at'];
    protected $casts = ['issued_at'=>'datetime'];

    public function batch() { return $this->belongsTo(StoreBatch::class, 'store_batch_id'); }
    public function issuedTo() { return $this->belongsTo(User::class, 'issued_to'); }

    protected static function booted() {
        static::created(function ($issue) {
            $issue->batch->decrement('current_stock', $issue->quantity_issued);
        });
    }
}