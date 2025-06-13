<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

// Включите отладку (уберите в продакшене)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Путь к базе данных (лучше использовать абсолютный путь)
$db_path = dirname(__FILE__) . 'D:\sqlite\database.db';

try {
    // Проверяем доступность SQLite
    if (!extension_loaded('pdo_sqlite')) {
        throw new Exception('SQLite extension not loaded');
    }

    // Подключаемся к БД
    $db = new PDO('sqlite:' . $db_path);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Создаем таблицу (если не существует)
    $db->exec("CREATE TABLE IF NOT EXISTS messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        subject TEXT NOT NULL,
        message TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP
    )");

    // Получаем данные
    $data = json_decode(file_get_contents('php://input'), true) ?? $_POST;

    // Валидация
    $required = ['name', 'email', 'subject', 'message'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            throw new Exception("Поле $field обязательно", 400);
        }
    }

    // Вставляем данные
    $stmt = $db->prepare("INSERT INTO messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
    $stmt->execute([
        htmlspecialchars($data['name']),
        htmlspecialchars($data['email']),
        htmlspecialchars($data['subject']),
        htmlspecialchars($data['message'])
    ]);

    echo json_encode(['success' => true, 'message' => 'Данные сохранены']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка: ' . $e->getMessage(),
        'db_path' => $db_path, // Для отладки
        'extensions' => get_loaded_extensions() // Для проверки SQLite
    ]);
}
?>