<?php
namespace App\Models;

use App\Core\Database;

class Lead
{
    public static function create(array $data): int
    {
        $sql = 'INSERT INTO leads (full_name, phone, product_id, flow_status) VALUES (:full_name, :phone, :product_id, :flow_status)';
        Database::query($sql, $data);
        return (int)Database::connection()->lastInsertId();
    }

    public static function count(): int
    {
        $stmt = Database::query('SELECT COUNT(*) FROM leads');
        return (int)$stmt->fetchColumn();
    }

    public static function filter(array $filters = []): array
    {
        $clauses = [];
        $params = [];
        if (!empty($filters['product_id'])) {
            $clauses[] = 'l.product_id = :product_id';
            $params['product_id'] = $filters['product_id'];
        }
        if (!empty($filters['date_from'])) {
            $clauses[] = 'DATE(l.created_at) >= :date_from';
            $params['date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $clauses[] = 'DATE(l.created_at) <= :date_to';
            $params['date_to'] = $filters['date_to'];
        }
        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        $sql = 'SELECT l.*, p.name as product_name FROM leads l LEFT JOIN products p ON p.id = l.product_id ' . $where . ' ORDER BY l.created_at DESC';
        $stmt = Database::query($sql, $params);
        return $stmt->fetchAll();
    }
}
