<?php
session_start();
include 'config.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['user_id'])) {
    $userId = intval($_POST['user_id']);

    // Удаляем пользователя
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    if ($stmt->execute()) {
        $_SESSION['message'] = "Lietotājs veiksmīgi dzēsts.";
    } else {
        $_SESSION['error'] = "Kļūda, dzēšot lietotāju.";
    }
    $stmt->close();
    header("Location: admin_users.php");
    exit;
}
?>