<?php
function clean($data) {
    $data = trim($data);            
    $data = stripslashes($data);    
    $data = htmlspecialchars($data);
    return $data;
}

function validate_registration($username, $email, $password) {
    $errors = [];
    
    if (empty($username) || strlen($username) < 2) {
        $errors[] = "Ім'я має містити мінімум 2 символи.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Некоректний формат Email.";
    }
    
    if (strlen($password) < 6) {
        $errors[] = "Пароль має бути не менше 6 символів.";
    }
    
    return $errors;
}

function validate_vacancy($title, $salary, $description) {
    $errors = [];
    
    if (empty($title) || strlen($title) < 3) {
        $errors[] = "Назва посади занадто коротка.";
    }
    
    if (empty($description) || strlen($description) < 10) {
        $errors[] = "Опис має бути детальним (мінімум 10 символів).";
    }
    
    if (strlen($title) > 100) {
        $errors[] = "Назва посади занадто довга (макс 100).";
    }
    
    return $errors;
}

function validate_file($file, $allowed_types, $max_size_mb = 5) {
    $errors = [];
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ["Помилка завантаження файлу."];
    }

    $fileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    $fileSize = $file["size"];
    
    if (!in_array($fileType, $allowed_types)) {
        $errors[] = "Цей тип файлу не дозволено. Доступні: " . implode(", ", $allowed_types);
    }
    
    if ($fileSize > $max_size_mb * 1024 * 1024) {
        $errors[] = "Файл занадто великий (Макс {$max_size_mb}MB).";
    }
    
    return $errors;
}
?>