<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Admin;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (isset($_SESSION['admin_id'])) {
            $this->redirect('/admin');
        }
        $this->view('admin/login');
    }

    public function login(): void
    {
        $this->csrfGuard();
        $email = sanitize($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $admin = Admin::findByEmail($email);
        if (!$admin || !password_verify($password, $admin['password'])) {
            flash('error', 'Login ma\'lumotlari noto\'g\'ri.');
            $this->redirect('/admin/login');
        }
        session_regenerate_id(true);
        $_SESSION['admin_id'] = $admin['id'];
        $this->redirect('/admin');
    }

    public function logout(): void
    {
        session_destroy();
        $this->redirect('/admin/login');
    }
}
