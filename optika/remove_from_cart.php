<?php
session_start();
include 'config.php';

if (isset($_POST['cart_id'])) {
    $cartId = intval($_POST['cart_id']);

    if (isset($_SESSION['user_id'])) {
        // Удаляем товар для авторизованного пользователя
        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
        $stmt->bind_param("i", $cartId);
    } else {
        // Удаляем товар из сессии для гостей
        unset($_SESSION['cart'][$cartId]);
    }

    if (isset($stmt) && !$stmt->execute()) {
        $_SESSION['message'] = "Kļūda, dzēšot no groza.";
    } else {
        $_SESSION['message'] = "Produkts veiksmīgi dzēsts no groza.";
    }

    if (isset($stmt)) $stmt->close();
    $conn->close();
}

header("Location: cart.php");
exit;