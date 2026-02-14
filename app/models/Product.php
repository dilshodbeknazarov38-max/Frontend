<?php
namespace App\Models;

use App\Core\Database;

class Product
{
    public static function count(): int
    {
        $stmt = Database::query('SELECT COUNT(*) as total FROM products');
        return (int)$stmt->fetchColumn();
    }

    public static function all(): array
    {
        $stmt = Database::query('SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC');
        return $stmt->fetchAll();
    }

    public static function active(): array
    {
        $stmt = Database::query('SELECT * FROM products WHERE status = :status ORDER BY created_at DESC', ['status' => 'active']);
        return $stmt->fetchAll();
    }

    public static function topSelling(int $limit = 4): array
    {
        $sql = 'SELECT p.*, COUNT(l.id) as lead_count FROM products p LEFT JOIN leads l ON l.product_id = p.id WHERE p.status = "active" GROUP BY p.id ORDER BY lead_count DESC, p.created_at DESC LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function search(string $term): array
    {
        $stmt = Database::query('SELECT * FROM products WHERE status = "active" AND name LIKE :term ORDER BY created_at DESC', ['term' => "%$term%"]);
        return $stmt->fetchAll();
    }

    public static function latest(int $limit = 8, ?int $categoryId = null, ?string $term = null): array
    {
        $clauses = ['status = "active"'];
        $params = [];
        if ($categoryId) {
            $clauses[] = 'category_id = :category_id';
            $params['category_id'] = $categoryId;
        }
        if ($term) {
            $clauses[] = 'name LIKE :term';
            $params['term'] = "%$term%";
        }
        $where = 'WHERE ' . implode(' AND ', $clauses);
        $sql = 'SELECT * FROM products ' . $where . ' ORDER BY created_at DESC LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        foreach ($params as $key => $value) {
            $paramType = is_int($value) ? \\PDO::PARAM_INT : \\PDO::PARAM_STR;
            $stmt->bindValue(':' . $key, $value, $paramType);
        }
        $stmt->bindValue(':limit', $limit, \\PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public static function find(int $id): ?array
    {
        $stmt = Database::query('SELECT * FROM products WHERE id = :id LIMIT 1', ['id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public static function findBySlug(string $slug): ?array
    {
        $stmt = Database::query('SELECT * FROM products WHERE slug = :slug AND status = "active" LIMIT 1', ['slug' => $slug]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public static function create(array $data): int
    {
        $data['slug'] = slugify($data['slug'] ?? $data['name']);
        if (empty($data['category_id'])) {
            $data['category_id'] = null;
        }
        $sql = 'INSERT INTO products (name, slug, description, price, image_path, video_url, flow_url, status, category_id) VALUES (:name, :slug, :description, :price, :image_path, :video_url, :flow_url, :status, :category_id)';
        Database::query($sql, $data);
        return (int)Database::connection()->lastInsertId();
    }

    public static function update(int $id, array $data): void
    {
        if (isset($data['slug'])) {
            $data['slug'] = slugify($data['slug']);
        }
        if (array_key_exists('category_id', $data) && empty($data['category_id'])) {
            $data['category_id'] = null;
        }
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = :$key";
        }
        $sql = 'UPDATE products SET ' . implode(', ', $fields) . ', updated_at = NOW() WHERE id = :id';
        $data['id'] = $id;
        Database::query($sql, $data);
    }

    public static function delete(int $id): void
    {
        Database::query('DELETE FROM products WHERE id = :id', ['id' => $id]);
    }

    public static function duplicate(int $id): ?int
    {
        $product = self::find($id);
        if (!$product) {
            return null;
        }
        return self::create([
            'category_id' => $product['category_id'],
            'name' => $product['name'] . ' (nusxa)',
            'slug' => slugify($product['slug'] . '-' . uniqid()),
            'description' => $product['description'],
            'price' => $product['price'],
            'image_path' => $product['image_path'],
            'video_url' => $product['video_url'],
            'flow_url' => $product['flow_url'],
            'status' => 'inactive',
        ]);
    }
}
