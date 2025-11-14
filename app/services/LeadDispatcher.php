<?php
namespace App\Services;

class LeadDispatcher
{
    public function sendToFlow(string $url, array $payload): array
    {
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
            $status = $response === false ? 500 : 200;
            $error = $response === false ? 'stream error' : '';
        }
        return [
            'status' => $status,
            'error' => $error,
            'response' => $response,
        ];
    }
}
