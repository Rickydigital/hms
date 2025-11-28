<?php
// app/Helpers/setting.php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (!function_exists('setting')) {
    /**
     * Get setting value with optional default
     */
    function setting($key, $default = null)
    {
        return Cache::rememberForever("setting_{$key}", function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            if (!$setting) return $default;

            return match ($setting->type ?? 'string') {
                'number'  => (float)$setting->value,
                'boolean' => in_array($setting->value, ['1', 'true', 'yes', 'on'], true),
                'json'    => json_decode($setting->value, true),
                default   => $setting->value,
            };
        });
    }
}