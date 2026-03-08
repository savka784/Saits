<?php
header('Content-Type: application/json; charset=utf-8');

$uploadDir = 'uploads/teachers/';
$response = ['success' => false, 'message' => '', 'filename' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $file = $_FILES['photo'];
    
    // Проверка на ошибки загрузки
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $response['message'] = 'Ошибка при загрузке файла';
        echo json_encode($response);
        exit;
    }
    
    // Проверка типа файла
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        $response['message'] = 'Недопустимый тип файла. Разрешены только изображения (JPEG, PNG, GIF, WebP)';
        echo json_encode($response);
        exit;
    }
    
    // Проверка размера файла (максимум 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        $response['message'] = 'Файл слишком большой. Максимальный размер: 5MB';
        echo json_encode($response);
        exit;
    }
    
    // Генерация уникального имени файла
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('teacher_') . '.' . $extension;
    $targetPath = $uploadDir . $filename;
    
    // Перемещение файла
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $response['success'] = true;
        $response['message'] = 'Фото успешно загружено';
        $response['filename'] = $filename;
        $response['path'] = $targetPath;
    } else {
        $response['message'] = 'Ошибка при сохранении файла';
    }
} else {
    $response['message'] = 'Файл не был отправлен';
}

echo json_encode($response);
?>
