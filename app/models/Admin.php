<?php
namespace App\Models;

use App\Core\Database;

class Admin
{
    public static function create(string $name, string $email, string $password): int
    {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        Database::query('INSERT INTO admins (name, email, password) VALUES (:name, :email, :password)', [
            'name' => $name,
            'email' => $email,
            'password' => $hash,
        ]);
        return (int)Database::connection()->lastInsertId();
    }

    public static function findByEmail(string $email): ?array
    {
        $stmt = Database::query('SELECT * FROM admins WHERE email = :email LIMIT 1', ['email' => $email]);
        $admin = $stmt->fetch();
        return $admin ?: null;
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::query('SELECT id, name, email FROM admins WHERE id = :id', ['id' => $id]);
        $admin = $stmt->fetch();
        return $admin ?: null;
    }
}
