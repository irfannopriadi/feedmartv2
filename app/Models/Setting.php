<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

#[Fillable(['key', 'value', 'group'])]
class Setting extends Model
{
    /**
     * Ambil nilai setting berdasarkan key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 3600, function () use ($key, $default) {
            $setting = static::where('key', $key)->first();

            return $setting ? $setting->value : $default;
        });
    }

    /**
     * Simpan atau update setting.
     */
    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group]
        );

        Cache::forget("setting_{$key}");
        Cache::forget('settings_all_keyed');
    }

    /**
     * Ambil seluruh setting sebagai array key => value.
     *
     * @return array<string, mixed>
     */
    public static function allKeyed(): array
    {
        return Cache::remember('settings_all_keyed', 3600, function () {
            return static::pluck('value', 'key')->toArray();
        });
    }
}
