<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Category;
use App\Models\Product;

class HomeController extends Controller
{
    public function index(): void
    {
        $categories = Category::all();
        $categoryId = isset($_GET['category']) ? (int)$_GET['category'] : null;
        $searchTerm = sanitize($_GET['q'] ?? '');
        $topProducts = Product::topSelling(4);
        $latestProducts = Product::latest(8, $categoryId, $searchTerm ?: null);

        $this->view('home/index', [
            'categories' => $categories,
            'topProducts' => $topProducts,
            'latestProducts' => $latestProducts,
            'searchTerm' => $searchTerm,
            'meta' => [
                'title' => 'CPA Mahsulotlar bosh sahifa',
                'description' => 'Eng ko\'p sotilgan va yangi CPA mahsulot landing sahifalari',
            ],
        ]);
    }
}
