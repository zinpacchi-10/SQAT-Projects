<?php
require_once __DIR__ . '/../core/Model.php';

class SettingModel extends Model
{
    public function get(string $key, $default = null)
    {
        $row = Database::fetchOne("SELECT setting_value FROM settings WHERE setting_key = ?", 's', [$key]);
        return $row ? $row['setting_value'] : $default;
    }

    public function set(string $key, string $value): bool
    {
        Database::execute(
            "INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value = ?",
            'sss',
            [$key, $value, $value]
        );
        return true;
    }
}
