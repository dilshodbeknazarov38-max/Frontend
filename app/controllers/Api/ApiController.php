<?php
namespace App\Controllers\Api;

use App\Core\Controller;

abstract class ApiController extends Controller
{
    protected ?string $apiToken = null;

    protected function authorize(): void
    {
        $token = extract_api_token();
        if (!validate_api_token($token)) {
            $this->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        $this->apiToken = $token;
    }

    protected function enforceRateLimit(string $context, ?int $max = null, ?int $decay = null): void
    {
        $maxRequests = $max ?? (int)(config('api.rate_limit.max_requests') ?? 60);
        $decaySeconds = $decay ?? (int)(config('api.rate_limit.decay_seconds') ?? 60);
        $key = implode(':', [$context, get_client_ip(), $this->apiToken ?? 'guest']);
        if (!rate_limit_allow($key, $maxRequests, $decaySeconds)) {
            $this->json(['success' => false, 'message' => 'Rate limit exceeded'], 429);
        }
    }

    protected function cors(array $methods): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: ' . implode(', ', $methods));
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
    }

    protected function json(array $payload, int $status = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode($payload);
        exit;
    }
}
