<?php
header('Content-Type: application/json');

// Проверяем, что запрос является POST-запросом
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Получаем данные из формы
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

// Валидация данных
if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'Все поля обязательны для заполнения']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Некорректный email адрес']);
    exit;
}

// Настройки подключения к базе данных
$dbHost = 'localhost';
$dbUser = 'root'; // Замените на вашего пользователя MySQL
$dbPass = ''; // Замените на ваш пароль MySQL
$dbName = 'nba_contact';

try {
    // Подключаемся к базе данных
    $conn = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Подготавливаем SQL-запрос
    $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
    
    // Привязываем параметры
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':subject', $subject);
    $stmt->bindParam(':message', $message);
    
    // Выполняем запрос
    $stmt->execute();
    
    // Возвращаем успешный ответ
    echo json_encode(['success' => true, 'message' => 'Сообщение успешно отправлено']);
    
} catch(PDOException $e) {
    // В случае ошибки возвращаем сообщение об ошибке
    echo json_encode(['success' => false, 'message' => 'Ошибка базы данных: ' . $e->getMessage()]);
}
?>