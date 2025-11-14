<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Setting;

class SettingsController extends Controller
{
    public function index(): void
    {
        $this->requireAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->csrfGuard();
            $pixel = sanitize($_POST['facebook_pixel'] ?? '');
            Setting::set('facebook_pixel', $pixel);
            flash('success', 'Sozlamalar saqlandi');
            $this->redirect('/admin/settings');
        }
        $pixel = Setting::get('facebook_pixel');
        $this->view('admin/settings/index', compact('pixel'));
    }
}
