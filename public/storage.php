<?php
$file = $_GET['file'] ?? '';
$path = __DIR__ . '/storage/' . $file;

if (!file_exists($path)) {
    http_response_code(404);
    echo "File not found at path: " . htmlspecialchars($path);
    exit;
}

$mime = mime_content_type($path);
header("Content-Type: $mime");
readfile($path);
exit;
