<?php
namespace App\Models;

use App\Core\Database;

class Category
{
    public static function all(): array
    {
        $stmt = Database::query('SELECT * FROM categories ORDER BY name');
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::query('SELECT * FROM categories WHERE id = :id LIMIT 1', ['id' => $id]);
        $category = $stmt->fetch();
        return $category ?: null;
    }

    public static function create(string $name): int
    {
        Database::query('INSERT INTO categories (name) VALUES (:name)', ['name' => $name]);
        return (int)Database::connection()->lastInsertId();
    }
}
