<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Receipt extends Model {
    use HasFactory;
    protected $fillable = ['visit_id','receipt_no','total_registration','total_final','grand_total','pdf_path','generated_by','generated_at'];
    protected $casts = [
        'total_registration'=>'decimal:2',
        'total_final'=>'decimal:2',
        'grand_total'=>'decimal:2',
        'generated_at'=>'datetime'
    ];

    public function visit() { return $this->belongsTo(Visit::class); }
    public function generatedBy() { return $this->belongsTo(User::class, 'generated_by'); }

    // Auto generate receipt number
    protected static function booted() {
        static::creating(function ($receipt) {
            if (empty($receipt->receipt_no)) {
                $latest = self::latest('id')->first();
                $number = $latest ? (int)substr($latest->receipt_no, 8) + 1 : 1;
                $receipt->receipt_no = 'RCPT' . date('Y') . str_pad($number, 6, '0', STR_PAD_LEFT);
            }
        });
    }
}