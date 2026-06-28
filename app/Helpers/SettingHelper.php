<?php

namespace App\Helpers;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingHelper
{
    /**
     * In-memory cache for settings loaded during the request lifecycle.
     */
    protected static array $cache = [];

    /**
     * Get a setting value by key.
     *
     * @param string $key
     * @param mixed $default
     * @param bool $useCache whether to use Redis/file cache as well
     * @return mixed
     */
    public static function get(string $key, $default = null, bool $useCache = true)
    {
        // Check local memory cache first
        if (array_key_exists($key, self::$cache)) {
            return self::$cache[$key];
        }

        if ($useCache) {
            $value = Cache::remember("setting_{$key}", 3600, function () use ($key) {
                $setting = Setting::where('key', $key)->first();
                return $setting ? $setting->value : null;
            });
        } else {
            $setting = Setting::where('key', $key)->first();
            $value = $setting ? $setting->value : null;
        }

        $value = $value ?? $default;
        self::$cache[$key] = $value;

        return $value;
    }

    /**
     * Set a setting value by key, and clear cache.
     *
     * @param string $key
     * @param mixed $value
     * @return Setting
     */
    public static function set(string $key, $value): Setting
    {
        $setting = Setting::updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        // Clear cache
        Cache::forget("setting_{$key}");
        self::$cache[$key] = $value;

        return $setting;
    }
}
