<?php

namespace App;

/**
 * @SWG\Definition(type="object", @SWG\Xml(name="User"))
 */

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Add a settings value
     *
     * @param mixed  $key
     * @param mixed  $val
     */
    public static function add($key, $val, string $type = 'string'): bool
    {
        if (self::has($key)) {
            return self::set($key, $val, $type);
        }

        return self::create(['name' => $key, 'val' => $val, 'type' => $type]) ? $val : false;
    }

    /**
     * Get a settings value
     *
     * @param null  $default
     * @param mixed $key
     *
     * @return bool|int|mixed
     */
    public static function get($key, $default = null)
    {
        if (self::has($key)) {
            $setting = self::getAllSettings()->where('name', $key)->first();

            return self::castValue($setting->val, $setting->type);
        }

        return $default;
    }

    /**
     * Set a value for setting
     *
     * @param mixed  $key
     * @param mixed  $val
     */
    public static function set($key, $val, string $type = 'string'): bool
    {
        if ($setting = self::getAllSettings()->where('name', $key)->first()) {
            return $setting->update([
                'name' => $key,
                'val'  => $val,
                'type' => $type,
            ]) ? $val : false;
        }

        return self::add($key, $val, $type);
    }

    /**
     * Remove a setting
     *
     * @param mixed $key
     */
    public static function remove($key): bool
    {
        if (self::has($key)) {
            return self::whereName($key)->delete();
        }

        return false;
    }

    /**
     * caste value into respective type
     *
     * @param mixed $val
     * @param mixed $castTo
     *
     * @return bool|int
     */
    private static function castValue($val, $castTo)
    {
        switch ($castTo) {
            case 'int':
            case 'integer':
                return intval($val);
                break;

            case 'bool':
            case 'boolean':
                return boolval($val);
                break;

            case 'float':
            case 'double':
                return floatval($val);
                break;

            default:
                return $val;
        }
    }

    public static function has($key)
    {
        return (bool) self::getAllSettings()->whereStrict('name', $key)->count();
    }

    /**
     * Get all the settings
     *
     * @return mixed
     */
    public static $allData = null;

    public static function getAllSettings()
    {
        if (self::$allData == null) {
            self::$allData = self::all();
        }

        return self::$allData;
    }

    public static function getErpLeadsCronSave()
    {
        return self::get('erp_leads_cron_save', 1);
    }
}
