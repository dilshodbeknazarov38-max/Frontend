<?php
namespace App\Controllers\Api;

use App\Models\Lead;
use App\Services\LeadDispatcher;

class FlowRetryController extends ApiController
{
    public function retry(): void
    {
        $this->authorize();
        $this->enforceRateLimit('api_retry', 10, 60);

        $failedLeads = Lead::failedWithProduct();
        if (!$failedLeads) {
            $this->json(['success' => true, 'processed' => 0, 'sent' => 0, 'failed' => 0]);
        }
        $dispatcher = new LeadDispatcher();
        $processed = $sent = $failed = 0;

        foreach ($failedLeads as $lead) {
            $processed++;
            $payload = [
                'full_name' => $lead['full_name'],
                'phone' => $lead['phone'],
                'product_id' => $lead['product_id'],
                'product_name' => $lead['product_name'],
                'slug' => $lead['slug'],
            ];
            $response = $dispatcher->sendToFlow($lead['flow_url'], $payload, (int)$lead['id']);
            $success = ($response['status'] ?? 0) >= 200 && ($response['status'] ?? 0) < 300;
            if ($success) {
                Lead::updateStatus((int)$lead['id'], 'sent', true);
                $sent++;
            } else {
                Lead::updateStatus((int)$lead['id'], 'failed', false);
                $failed++;
            }
        }

        $this->json([
            'success' => true,
            'processed' => $processed,
            'sent' => $sent,
            'failed' => $failed,
        ]);
    }
}
