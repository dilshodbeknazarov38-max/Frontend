<?php
namespace App\Services;

use App\Models\FlowLog;

class LeadDispatcher
{
    public function sendToFlow(string $url, array $payload, ?int $leadId = null): array
    {
        $response = '';
        $error = '';
        $status = 0;

        if (function_exists('curl_init')) {
            $ch = curl_init($url);
            if ($ch !== false) {
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_TIMEOUT => 10,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query($payload),
                ]);
                $response = curl_exec($ch);
                $error = curl_error($ch);
                $status = curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
                curl_close($ch);
            } else {
                $response = false;
                $status = 500;
                $error = 'curl init failed';
            }
        } else {
            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\\r\\n",
                    'method' => 'POST',
                    'content' => http_build_query($payload),
                    'timeout' => 10,
                ],
            ];
            $context = stream_context_create($options);
            $response = @file_get_contents($url, false, $context);
            $status = $response === false ? self::detectStreamStatus() : 200;
            $error = $response === false ? 'stream error' : '';
        }

        $responseBody = is_string($response) ? $response : '';

        if ($leadId !== null) {
            FlowLog::create([
                'lead_id' => $leadId,
                'request_payload' => $payload,
                'response_status' => $status,
                'response_body' => $responseBody ?: $error,
            ]);
        }

        return [
            'status' => $status,
            'error' => $error,
            'response' => $responseBody,
        ];
    }

    private static function detectStreamStatus(): int
    {
        global $http_response_header;
        if (!empty($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
            return (int)$matches[1];
        }
        return 500;
    }
}
