<?php
namespace App\Core;

class Controller
{
    protected function view(string $template, array $data = []): void
    {
        echo view($template, $data);
    }

    protected function redirect(string $path): void
    {
        header('Location: ' . url($path));
        exit;
    }

    protected function requireAuth(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect('/admin/login');
        }
    }

    protected function csrfGuard(): void
    {
        $token = $_POST['_token'] ?? '';
        if (!verify_csrf($token)) {
            http_response_code(419);
            exit('CSRF token mismatch.');
        }
        rotate_csrf_token();
    }
}
