<?php
include 'config.php';

// Данные администратора
$name = "Admin Name"; // Имя администратора
$email = "admin@example.com"; // Email администратора
$password = "admin123"; // Пароль администратора (в читаемом виде)
$role = "admin"; // Роль администратора

// Хэшируем пароль
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// SQL-запрос для добавления администратора
$query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssss", $name, $email, $hashedPassword, $role);

if ($stmt->execute()) {
    echo "Администратор успешно создан!";
    echo "<br>Email: $email";
    echo "<br>Пароль: $password";
} else {
    echo "Ошибка: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>