<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description', 'is_public'];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Obtener valor con tipo
    public static function getValue($key, $default = null)
    {
        $setting = self::where('key', $key)->first();
        if (!$setting) return $default;

        return match ($setting->type) {
            'boolean' => filter_var($setting->value, FILTER_VALIDATE_BOOLEAN),
            'number' => (float) $setting->value,
            'json' => json_decode($setting->value, true),
            'image' => $setting->value,
            default => $setting->value,
        };
    }

    public static function setValue($key, $value, $type = 'string', $group = 'general', $label = null, $description = null, $isPublic = false)
    {
        return self::updateOrCreate(
            ['key' => $key],
            [
                'value' => is_array($value) ? json_encode($value) : (string) $value,
                'type' => $type,
                'group' => $group,
                'label' => $label,
                'description' => $description,
                'is_public' => $isPublic,
            ]
        );
    }

    // Obtener todas las configuraciones por grupo
    public static function getGroup($group)
    {
        return self::where('group', $group)->get()->mapWithKeys(function ($item) {
            return [$item->key => self::getValue($item->key)];
        });
    }

    // Obtener configuraciones públicas
    public static function getPublic()
    {
        return self::where('is_public', true)->get()->mapWithKeys(function ($item) {
            return [$item->key => self::getValue($item->key)];
        });
    }
}