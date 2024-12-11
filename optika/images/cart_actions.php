<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update' && isset($_POST['cart_id'], $_POST['quantity'])) {
        $cartId = intval($_POST['cart_id']);
        $quantity = max(1, intval($_POST['quantity']));

        if (isset($_SESSION['user_id'])) {
            // Обновление в базе данных
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $quantity, $cartId);
            $stmt->execute();
            $stmt->close();
        } else {
            // Обновление для гостя
            $_SESSION['cart'][$cartId] = $quantity;
        }
    } elseif ($action === 'delete' && isset($_POST['cart_id'])) {
        $cartId = intval($_POST['cart_id']);

        if (isset($_SESSION['user_id'])) {
            // Удаление из базы данных
            $stmt = $conn->prepare("DELETE FROM cart WHERE id = ?");
            $stmt->bind_param("i", $cartId);
            $stmt->execute();
            $stmt->close();
        } else {
            // Удаление для гостя
            unset($_SESSION['cart'][$cartId]);
        }
    }
}

// Перенаправление обратно в корзину
header("Location: cart.php");
exit;