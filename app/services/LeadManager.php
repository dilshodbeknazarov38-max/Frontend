<?php
namespace App\Services;

use App\Models\Lead;

class LeadManager
{
    public function submit(array $formData, array $product): array
    {
        $leadId = Lead::create([
            'full_name' => $formData['full_name'],
            'phone' => $formData['phone'],
            'product_id' => $product['id'],
            'flow_status' => 'pending',
        ]);

        $dispatcher = new LeadDispatcher();
        $payload = [
            'full_name' => $formData['full_name'],
            'phone' => $formData['phone'],
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'slug' => $product['slug'],
        ];
        $response = $dispatcher->sendToFlow($product['flow_url'], $payload, $leadId);
        $status = ($response['status'] ?? 0) >= 200 && ($response['status'] ?? 0) < 300 ? 'sent' : 'failed';
        Lead::updateStatus($leadId, $status, false);
        return [
            'lead_id' => $leadId,
            'status' => $status,
            'response' => $response,
        ];
    }
}
