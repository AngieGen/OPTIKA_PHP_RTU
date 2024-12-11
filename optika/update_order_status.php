<?php
session_start();
include 'config.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Проверяем, получены ли данные
if (!isset($_POST['order_id']) || !isset($_POST['status'])) {
    die("Nepareizi dati.");
}

$orderId = intval($_POST['order_id']);
$status = mysqli_real_escape_string($conn, $_POST['status']);

// Обновляем статус заказа
$query = "UPDATE orders SET status = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $orderId);

if ($stmt->execute()) {
    $_SESSION['message'] = "Pasūtījuma statuss veiksmīgi atjaunināts.";
} else {
    $_SESSION['message'] = "Kļūda, atjauninot pasūtījuma statusu.";
}

$stmt->close();
$conn->close();

header("Location: view_order.php?id=" . $orderId);
exit;
?>