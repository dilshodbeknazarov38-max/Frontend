<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Lead;
use App\Models\Product;
use App\Models\Setting;
use App\Services\LeadDispatcher;

class LandingController extends Controller
{
    public function show(string $slug): void
    {
        $product = Product::findBySlug($slug);
        if (!$product) {
            http_response_code(404);
            echo view('errors/404');
            return;
        }
        $pixelId = Setting::get('facebook_pixel');
        $videoUrl = $this->formatVideoUrl($product['video_url'] ?? null);
        $this->view('landing/show', compact('product', 'pixelId', 'videoUrl'));
    }

    public function lead(string $slug)
    {
        $this->csrfGuard();
        $product = Product::findBySlug($slug);
        if (!$product) {
            return $this->json(['success' => false, 'message' => 'Mahsulot topilmadi']);
        }
        $data = [
            'full_name' => sanitize($_POST['full_name'] ?? ''),
            'phone' => sanitize($_POST['phone'] ?? ''),
        ];
        $errors = Validator::make($data, [
            'full_name' => 'required|max:120',
            'phone' => 'required|max:20',
        ]);
        if ($errors) {
            return $this->json(['success' => false, 'message' => current($errors)]);
        }
        $dispatcher = new LeadDispatcher();
        $payload = [
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'product_id' => $product['id'],
            'product_name' => $product['name'],
            'slug' => $product['slug'],
        ];
        $response = $dispatcher->sendToFlow($product['flow_url'], $payload);
        $status = ($response['status'] ?? 0) >= 200 && ($response['status'] ?? 0) < 300 ? 'success' : 'failed';
        Lead::create([
            'full_name' => $data['full_name'],
            'phone' => $data['phone'],
            'product_id' => $product['id'],
            'flow_status' => $status,
        ]);
        return $this->json([
            'success' => true,
            'message' => 'Buyurtmangiz qabul qilindi! Operator tez orada bog\'lanadi.',
        ]);
    }

    private function json(array $payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function formatVideoUrl(?string $url): ?string
    {
        if (!$url) {
            return null;
        }
        $url = trim($url);
        if ($url === '') {
            return null;
        }
        $lower = strtolower($url);
        if (str_contains($lower, 'youtube')) {
            $url = preg_replace('/watch\\?v=/', 'embed/', $url, 1);
        }
        if (str_contains($lower, 'youtu.be')) {
            $path = parse_url($url, PHP_URL_PATH);
            $videoId = $path ? ltrim($path, '/') : '';
            if ($videoId) {
                $url = 'https://www.youtube.com/embed/' . $videoId;
            }
        }
        return $url;
    }
}
