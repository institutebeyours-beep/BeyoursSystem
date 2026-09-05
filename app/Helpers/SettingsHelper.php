<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingsHelper
{
    /**
     * Obtener valor de una configuración con cache
     */
    public static function get($key, $default = null)
    {
        $cacheKey = 'setting_' . $key;
        
        return Cache::remember($cacheKey, 3600, function () use ($key, $default) {
            $setting = Setting::where('key', $key)->first();
            \Log::info('🔍 SettingsHelper - Cargando: ' . $key);
            return $setting ? $setting->value : $default;
        });
    }
    
    /**
     * Actualizar valor y limpiar cache
     */
    public static function set($key, $value)
    {
        $setting = Setting::where('key', $key)->first();
        if ($setting) {
            $setting->value = $value;
            $setting->save();
            
            // Limpiar cache
            Cache::forget('setting_' . $key);
            
            \Log::info('✅ SettingsHelper - Actualizado: ' . $key . ' = ' . $value);
        }
    }
    
    /**
     * Limpiar toda la cache de settings
     */
    public static function clearCache()
    {
        $keys = Setting::pluck('key')->toArray();
        foreach ($keys as $key) {
            Cache::forget('setting_' . $key);
        }
        \Log::info('🧹 SettingsHelper - Cache limpiado');
    }
}