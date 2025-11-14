<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lead;
use App\Models\Product;

class LeadController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $filters = [
            'product_id' => isset($_GET['product_id']) && $_GET['product_id'] !== '' ? (int)$_GET['product_id'] : null,
            'date_from' => trim($_GET['date_from'] ?? '') ?: null,
            'date_to' => trim($_GET['date_to'] ?? '') ?: null,
        ];
        $leads = Lead::filter($filters);
        $products = Product::all();
        $exportQuery = http_build_query(array_filter($filters, fn($v) => $v !== null && $v !== ''));
        $this->view('admin/leads/index', compact('leads', 'products', 'filters', 'exportQuery'));
    }

    public function export(): void
    {
        $this->requireAuth();
        $filters = [
            'product_id' => isset($_GET['product_id']) && $_GET['product_id'] !== '' ? (int)$_GET['product_id'] : null,
            'date_from' => trim($_GET['date_from'] ?? '') ?: null,
            'date_to' => trim($_GET['date_to'] ?? '') ?: null,
        ];
        $leads = Lead::filter($filters);
        header('Content-Type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="leads-' . date('Ymd-His') . '.csv"');
        $out = fopen('php://output', 'w');
        fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF));
        fputcsv($out, ['ID', 'Ism', 'Telefon', 'Mahsulot', 'Sana']);
        foreach ($leads as $lead) {
            fputcsv($out, [
                $lead['id'],
                $lead['full_name'],
                $lead['phone'],
                $lead['product_name'],
                $lead['created_at'],
            ]);
        }
        fclose($out);
        exit;
    }
}
