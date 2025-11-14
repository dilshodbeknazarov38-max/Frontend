<?php
namespace App\Controllers\Api;

use App\Models\FlowLog;

class FlowLogController extends ApiController
{
    public function index(): void
    {
        $this->authorize();
        $this->enforceRateLimit('api_flow_logs', 30, 60);
        $filters = [];
        if (!empty($_GET['lead_id'])) {
            $filters['lead_id'] = (int)$_GET['lead_id'];
        }
        if (!empty($_GET['status'])) {
            $filters['status'] = (int)$_GET['status'];
        }
        $logs = FlowLog::filter($filters);
        $this->json([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
