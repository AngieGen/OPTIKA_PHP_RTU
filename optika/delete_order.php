<?php
session_start();
include 'config.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Проверяем наличие ID заказа
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Nepareizs pasūtījuma ID.");
}

$orderId = intval($_GET['id']);

// Удаляем товары из заказа
$itemQuery = "DELETE FROM order_items WHERE order_id = ?";
$itemStmt = $conn->prepare($itemQuery);
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();

// Удаляем заказ
$orderQuery = "DELETE FROM orders WHERE id = ?";
$orderStmt = $conn->prepare($orderQuery);
$orderStmt->bind_param("i", $orderId);
$orderStmt->execute();

$itemStmt->close();
$orderStmt->close();

$_SESSION['message'] = "Pasūtījums veiksmīgi dzēsts.";
header("Location: admin_orders.php");
exit;
?>