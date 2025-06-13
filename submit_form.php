<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Неверный метод запроса']);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Некорректный email адрес']);
    exit;
}

$data = [
    'name' => $name,
    'email' => $email,
    'subject' => $subject,
    'message' => $message,
    'date' => date('Y-m-d H:i:s')
];

$filename = 'messages.json';

try {
    $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE) . "\n";
    
    if (file_put_contents($filename, $jsonData, FILE_APPEND | LOCK_EX) !== false) {
        echo json_encode(['success' => true, 'message' => 'Сообщение успешно сохранено']);
    } else {
        throw new Exception('Ошибка записи в файл');
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Ошибка: ' . $e->getMessage()]);
}
?>