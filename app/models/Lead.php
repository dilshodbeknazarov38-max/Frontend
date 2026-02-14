<?php
namespace App\Models;

use App\Core\Database;
use PDO;

class Lead
{
    public static function create(array $data): int
    {
        $defaults = [
            'flow_status' => 'pending',
            'retry_count' => 0,
        ];
        $payload = array_merge($defaults, $data);
        $sql = 'INSERT INTO leads (full_name, phone, product_id, flow_status, retry_count) VALUES (:full_name, :phone, :product_id, :flow_status, :retry_count)';
        Database::query($sql, $payload);
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

    public static function updateStatus(int $leadId, string $status, bool $incrementRetry = false): void
    {
        $allowed = ['pending', 'sent', 'failed'];
        if (!in_array($status, $allowed, true)) {
            $status = 'failed';
        }
        $sql = 'UPDATE leads SET flow_status = :status, retry_count = retry_count ' . ($incrementRetry ? '+ 1' : '+ 0') . ' WHERE id = :id';
        Database::query($sql, [
            'status' => $status,
            'id' => $leadId,
        ]);
    }

    public static function failedWithProduct(int $limit = 100): array
    {
        $sql = 'SELECT l.*, p.name as product_name, p.flow_url, p.slug FROM leads l INNER JOIN products p ON p.id = l.product_id WHERE l.flow_status = :status ORDER BY l.created_at ASC LIMIT :limit';
        $stmt = Database::connection()->prepare($sql);
        $stmt->bindValue(':status', 'failed');
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
