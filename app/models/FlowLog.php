<?php
namespace App\Models;

use App\Core\Database;

class FlowLog
{
    public static function create(array $data): void
    {
        $sql = 'INSERT INTO flow_logs (lead_id, request_payload, response_status, response_body) VALUES (:lead_id, :request_payload, :response_status, :response_body)';
        Database::query($sql, [
            'lead_id' => $data['lead_id'],
            'request_payload' => json_encode($data['request_payload'], JSON_UNESCAPED_UNICODE),
            'response_status' => $data['response_status'],
            'response_body' => $data['response_body'],
        ]);
    }

    public static function filter(array $filters = []): array
    {
        $clauses = [];
        $params = [];
        if (!empty($filters['lead_id'])) {
            $clauses[] = 'lead_id = :lead_id';
            $params['lead_id'] = (int)$filters['lead_id'];
        }
        if (!empty($filters['status'])) {
            $clauses[] = 'response_status = :response_status';
            $params['response_status'] = (int)$filters['status'];
        }
        $where = $clauses ? 'WHERE ' . implode(' AND ', $clauses) : '';
        $sql = 'SELECT * FROM flow_logs ' . $where . ' ORDER BY created_at DESC LIMIT 200';
        $stmt = Database::query($sql, $params);
        return $stmt->fetchAll();
    }
}
