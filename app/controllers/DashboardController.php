<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lead;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $productCount = Product::count();
        $leadCount = Lead::count();
        $this->view('admin/dashboard', compact('productCount', 'leadCount'));
    }
}
