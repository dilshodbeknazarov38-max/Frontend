<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Validator;
use App\Models\Category;
use App\Models\Product;
use App\Services\FileUploader;

class ProductController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        $products = Product::all();
        $this->view('admin/products/index', compact('products'));
    }

    public function create(): void
    {
        $this->requireAuth();
        $categories = Category::all();
        $this->view('admin/products/form', compact('categories'));
    }

    public function store(): void
    {
        $this->requireAuth();
        $this->csrfGuard();
        $input = [
            'name' => sanitize($_POST['name'] ?? ''),
            'slug' => sanitize($_POST['slug'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'price' => trim($_POST['price'] ?? ''),
            'flow_url' => trim($_POST['flow_url'] ?? ''),
            'video_url' => trim($_POST['video_url'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
            'category_id' => (int)($_POST['category_id'] ?? 0),
        ];
        if ($input['video_url'] === '') {
            $input['video_url'] = null;
        }
        if (!in_array($input['status'], ['active', 'inactive'], true)) {
            $input['status'] = 'inactive';
        }
        $errors = Validator::make($input, [
            'name' => 'required|max:180',
            'price' => 'required|numeric',
            'flow_url' => 'required|url',
        ]);
        if (!empty($input['video_url']) && !filter_var($input['video_url'], FILTER_VALIDATE_URL)) {
            $errors['video_url'] = 'Video URL noto\'g\'ri.';
        }
        if (!$input['category_id']) {
            $errors['category_id'] = 'Kategoriya tanlang.';
        }
        if ($errors) {
            flash('error', current($errors));
            $this->redirect('/admin/products/create');
        }
        if (empty($_FILES['image']['tmp_name'] ?? null)) {
            flash('error', 'Rasmni tanlang.');
            $this->redirect('/admin/products/create');
        }
        $uploader = new FileUploader();
        try {
            $imagePath = $uploader->uploadImage($_FILES['image']);
        } catch (\RuntimeException $e) {
            flash('error', $e->getMessage());
            $this->redirect('/admin/products/create');
        }
        if (!$imagePath) {
            flash('error', 'Rasmni yuklashda xatolik.');
            $this->redirect('/admin/products/create');
        }
        $input['image_path'] = $imagePath;
        Product::create($input);
        flash('success', 'Mahsulot muvaffaqiyatli yaratildi.');
        $this->redirect('/admin/products');
    }

    public function edit(int $id): void
    {
        $this->requireAuth();
        $product = Product::find($id);
        if (!$product) {
            flash('error', 'Mahsulot topilmadi');
            $this->redirect('/admin/products');
        }
        $categories = Category::all();
        $this->view('admin/products/form', compact('product', 'categories'));
    }

    public function update(int $id): void
    {
        $this->requireAuth();
        $this->csrfGuard();
        $product = Product::find($id);
        if (!$product) {
            flash('error', 'Mahsulot mavjud emas');
            $this->redirect('/admin/products');
        }
        $input = [
            'name' => sanitize($_POST['name'] ?? ''),
            'slug' => sanitize($_POST['slug'] ?? $product['slug']),
            'description' => trim($_POST['description'] ?? $product['description']),
            'price' => trim($_POST['price'] ?? $product['price']),
            'flow_url' => trim($_POST['flow_url'] ?? $product['flow_url']),
            'video_url' => trim($_POST['video_url'] ?? $product['video_url']),
            'status' => $_POST['status'] ?? $product['status'],
            'category_id' => (int)($_POST['category_id'] ?? $product['category_id']),
        ];
        if ($input['video_url'] === '') {
            $input['video_url'] = null;
        }
        if (!in_array($input['status'], ['active', 'inactive'], true)) {
            $input['status'] = 'inactive';
        }
        $errors = Validator::make($input, [
            'name' => 'required|max:180',
            'price' => 'required|numeric',
            'flow_url' => 'required|url',
        ]);
        if (!empty($input['video_url']) && !filter_var($input['video_url'], FILTER_VALIDATE_URL)) {
            $errors['video_url'] = 'Video URL noto\'g\'ri.';
        }
        if (!$input['category_id']) {
            $errors['category_id'] = 'Kategoriya tanlang.';
        }
        if ($errors) {
            flash('error', current($errors));
            $this->redirect('/admin/products/' . $id . '/edit');
        }
        if (!empty($_FILES['image']['tmp_name'])) {
            $uploader = new FileUploader();
            try {
                $newImage = $uploader->uploadImage($_FILES['image']);
            } catch (\RuntimeException $e) {
                flash('error', $e->getMessage());
                $this->redirect('/admin/products/' . $id . '/edit');
            }
            if (!$newImage) {
                flash('error', 'Rasmni yuklashda xatolik.');
                $this->redirect('/admin/products/' . $id . '/edit');
            }
            $input['image_path'] = $newImage;
        }
        Product::update($id, $input);
        flash('success', 'Mahsulot yangilandi');
        $this->redirect('/admin/products');
    }

    public function delete(int $id): void
    {
        $this->requireAuth();
        $this->csrfGuard();
        $product = Product::find($id);
        if (!$product) {
            flash('error', 'Mahsulot topilmadi');
            $this->redirect('/admin/products');
        }
        Product::delete($id);
        flash('success', 'Mahsulot o\'chirildi');
        $this->redirect('/admin/products');
    }

    public function duplicate(int $id): void
    {
        $this->requireAuth();
        $this->csrfGuard();
        $newId = Product::duplicate($id);
        if (!$newId) {
            flash('error', 'Mahsulot topilmadi');
        } else {
            flash('success', 'Mahsulot nusxalandi');
        }
        $this->redirect('/admin/products');
    }
}
