<?php
namespace App\Models;

use App\Core\Database;

class Setting
{
    public static function get(string $key, $default = null)
    {
        $stmt = Database::query('SELECT value FROM settings WHERE `key` = :key LIMIT 1', ['key' => $key]);
        $value = $stmt->fetchColumn();
        return $value !== false ? $value : $default;
    }

    public static function set(string $key, string $value): void
    {
        $existing = self::get($key);
        if ($existing === null) {
            Database::query('INSERT INTO settings (`key`, `value`) VALUES (:key, :value)', ['key' => $key, 'value' => $value]);
        } else {
            Database::query('UPDATE settings SET `value` = :value WHERE `key` = :key', ['key' => $key, 'value' => $value]);
        }
    }
}
