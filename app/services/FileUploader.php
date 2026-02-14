<?php
namespace App\Services;

class FileUploader
{
    private array $allowedMime = ['image/jpeg', 'image/png', 'image/webp'];

    public function uploadImage(array $file): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }
        if (($file['size'] ?? 0) > 3 * 1024 * 1024) {
            throw new \RuntimeException('Rasm hajmi 3MB dan oshmasligi kerak.');
        }
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($file['tmp_name']);
        if (!in_array($mime, $this->allowedMime, true)) {
            throw new \RuntimeException('Ruxsat etilmagan fayl turi.');
        }
        $extension = match ($mime) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            default => 'jpg',
        };
        $filename = uniqid('img_') . '.' . $extension;
        $uploadDir = PUBLIC_PATH . '/uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        $destination = $uploadDir . $filename;
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            throw new \RuntimeException('Faylni yuklash mumkin bo\'lmadi.');
        }
        return '/uploads/' . $filename;
    }
}
