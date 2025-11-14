<?php
namespace App\Controllers\Api;

use App\Core\Database;

class HealthController extends ApiController
{
    public function index(): void
    {
        $this->authorize();
        $this->enforceRateLimit('api_health', 30, 60);
        try {
            Database::connection()->query('SELECT 1');
            $dbStatus = 'connected';
        } catch (\Throwable $th) {
            $dbStatus = 'error';
        }
        $this->json([
            'status' => 'ok',
            'db' => $dbStatus,
            'version' => '1.0',
        ]);
    }
}
