<?php
declare(strict_types=1);

/**
 * Subida segura de imágenes:
 *  - tipo MIME real con finfo (no se confía en el nombre ni en el Content-Type del navegador)
 *  - verificación de que es una imagen decodificable (getimagesize)
 *  - tamaño máximo configurable (UPLOAD_MAX_MB)
 *  - nombre aleatorio y almacenamiento en /storage/uploads, fuera de /public y del código
 */
final class Upload
{
    public const ALLOWED = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];

    /** @return array{ok: bool, id?: int, error?: string} */
    public static function image(?array $file, string $altText = ''): array
    {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return ['ok' => false, 'error' => 'No se seleccionó ninguna imagen.'];
        }
        $max = (int) config('upload_max_bytes');
        $maxMb = round($max / 1048576, 1);
        $errors = [
            UPLOAD_ERR_INI_SIZE   => "La imagen supera el tamaño máximo permitido ({$maxMb} MB).",
            UPLOAD_ERR_FORM_SIZE  => "La imagen supera el tamaño máximo permitido ({$maxMb} MB).",
            UPLOAD_ERR_PARTIAL    => 'La imagen se subió de forma incompleta. Inténtalo de nuevo.',
            UPLOAD_ERR_NO_TMP_DIR => 'El servidor no tiene carpeta temporal configurada.',
            UPLOAD_ERR_CANT_WRITE => 'El servidor no pudo guardar la imagen.',
        ];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => $errors[$file['error']] ?? 'No se pudo subir la imagen.'];
        }
        if (!is_uploaded_file($file['tmp_name']) && PHP_SAPI !== 'cli') {
            return ['ok' => false, 'error' => 'Archivo no válido.'];
        }
        if ($file['size'] > $max || filesize($file['tmp_name']) > $max) {
            return ['ok' => false, 'error' => "La imagen supera el tamaño máximo permitido ({$maxMb} MB)."];
        }
        $mime = (new finfo(FILEINFO_MIME_TYPE))->file($file['tmp_name']) ?: '';
        if (!isset(self::ALLOWED[$mime])) {
            return ['ok' => false, 'error' => 'Formato no permitido. Usa JPG, PNG o WEBP.'];
        }
        try {
            $info = getimagesize($file['tmp_name']);
        } catch (Throwable) {
            $info = false;
        }
        if ($info === false || $info[0] < 1 || $info[1] < 1 || $info[0] > 8000 || $info[1] > 8000) {
            return ['ok' => false, 'error' => 'El archivo no es una imagen válida.'];
        }

        $dir = config('uploads_path');
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }
        $name = random_token(16) . '.' . self::ALLOWED[$mime];
        $dest = $dir . '/' . $name;
        $moved = PHP_SAPI === 'cli' ? copy($file['tmp_name'], $dest) : move_uploaded_file($file['tmp_name'], $dest);
        if (!$moved) {
            return ['ok' => false, 'error' => 'El servidor no pudo guardar la imagen.'];
        }
        @chmod($dest, 0644);

        $original = mb_substr(basename((string) $file['name']), 0, 255);
        $id = Db::insert(
            'INSERT INTO media (file_name, original_name, mime_type, size_bytes, width, height, alt_text, created_by) VALUES (?,?,?,?,?,?,?,?)',
            [$name, $original, $mime, (int) filesize($dest), $info[0], $info[1], mb_substr($altText, 0, 255), Auth::id()]
        );
        return ['ok' => true, 'id' => $id];
    }

    /** Sirve un archivo de /storage/uploads con cabeceras seguras. */
    public static function serve(int $id): never
    {
        $media = Db::one('SELECT * FROM media WHERE id = ?', [$id]);
        $path = $media ? config('uploads_path') . '/' . basename($media['file_name']) : null;
        if (!$media || !is_file($path)) {
            http_response_code(404);
            exit;
        }
        $etag = '"' . md5($media['file_name'] . filemtime($path)) . '"';
        header('ETag: ' . $etag);
        header('Cache-Control: public, max-age=604800');
        header('X-Content-Type-Options: nosniff');
        if ($media['mime_type'] === 'image/svg+xml') {
            // Solo las ilustraciones de ejemplo instaladas por el seeder son SVG.
            header("Content-Security-Policy: default-src 'none'; style-src 'unsafe-inline'");
        }
        if (($_SERVER['HTTP_IF_NONE_MATCH'] ?? '') === $etag) {
            http_response_code(304);
            exit;
        }
        header('Content-Type: ' . $media['mime_type']);
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }
}
