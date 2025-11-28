<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['key', 'value', 'type', 'description', 'is_visible', 'sort_order'];
    public $timestamps = true;

    // Auto cast value based on type
    protected $casts = [
        'is_visible' => 'boolean',
        'value'      => 'string',
    ];

    // Magic helper: Setting::get('registration_fee') → 200
    public static function get($key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = static::where('key', $key)->first();
            if (!$setting) return $default;

            return match ($setting->type) {
                'number'  => (float)$setting->value,
                'boolean' => $setting->value === 'true' || $setting->value === '1',
                'json'    => json_decode($setting->value, true),
                default   => $setting->value,
            };
        });
    }

    // Clear cache on update
    protected static function booted()
    {
        static::saved(fn($s) => Cache::forget("setting_{$s->key}"));
        static::deleted(fn($s) => Cache::forget("setting_{$s->key}"));
    }
}