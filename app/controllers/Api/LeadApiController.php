<?php
namespace App\Controllers\Api;

use App\Core\Validator;
use App\Models\Product;
use App\Services\LeadManager;

class LeadApiController extends ApiController
{
    public function options(): void
    {
        $this->cors(['POST', 'OPTIONS']);
        http_response_code(204);
        exit;
    }

    public function store(): void
    {
        $this->cors(['POST', 'OPTIONS']);
        $this->authorize();
        $this->enforceRateLimit('api_lead', 30, 60);

        $inputs = $this->input();
        $data = [
            'full_name' => sanitize($inputs['full_name'] ?? ''),
            'phone' => sanitize($inputs['phone'] ?? ''),
            'product_slug' => sanitize($inputs['product_slug'] ?? '') ?: null,
        ];
        $errors = Validator::make($data, [
            'full_name' => 'required|max:120',
            'phone' => 'required|max:20',
        ]);
        if ($errors) {
            $this->json(['success' => false, 'message' => current($errors)], 422);
        }
        if (empty($data['product_slug']) && empty($inputs['product_id'])) {
            $this->json(['success' => false, 'message' => 'Mahsulot slug yoki ID talab qilinadi'], 422);
        }
        $product = null;
        if (!empty($data['product_slug'])) {
            $product = Product::findBySlug($data['product_slug']);
        } elseif (!empty($inputs['product_id'])) {
            $product = Product::find((int)$inputs['product_id']);
        }
        if (!$product || $product['status'] !== 'active') {
            $this->json(['success' => false, 'message' => 'Mahsulot topilmadi yoki noaktiv'], 404);
        }
        $manager = new LeadManager();
        $result = $manager->submit($data, $product);
        $success = $result['status'] === 'sent';
        $this->json([
            'success' => $success,
            'lead_id' => $result['lead_id'],
            'status' => $result['status'],
            'message' => $success ? 'OK' : 'Flow yuborishda xatolik',
        ], $success ? 200 : 500);
    }

    private function input(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? $_SERVER['HTTP_CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $raw = file_get_contents('php://input');
            $json = json_decode($raw, true);
            if (is_array($json)) {
                return $json;
            }
        }
        return $_POST;
    }
}
