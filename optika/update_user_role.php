<?php
session_start();
include 'config.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id']) && isset($_POST['role'])) {
    $userId = intval($_POST['user_id']);
    $newRole = mysqli_real_escape_string($conn, $_POST['role']);

    // Обновляем роль пользователя
    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $newRole, $userId);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Lietotāja loma veiksmīgi atjaunināta.";
    } else {
        $_SESSION['error'] = "Kļūda, atjauninot lietotāja lomu.";
    }
    $stmt->close();
    header("Location: admin_users.php");
    exit;
}
?>