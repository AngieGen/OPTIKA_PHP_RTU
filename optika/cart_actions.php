<?php
session_start();
include 'config.php';

// Проверяем, передано ли действие
if (!isset($_POST['action'])) {
    header("Location: cart.php");
    exit;
}

$action = $_POST['action'];

// Если пользователь авторизован
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Обработка действия
    if ($action === 'update' && isset($_POST['cart_id'], $_POST['quantity'])) {
        $cartId = intval($_POST['cart_id']);
        $quantity = max(1, intval($_POST['quantity'])); // Минимум 1

        $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ? AND user_id = ?");
        $stmt->bind_param("iii", $quantity, $cartId, $userId);
        $stmt->execute();
        $stmt->close();
    } elseif ($action === 'delete' && isset($_POST['cart_id'])) {
        $cartId = intval($_POST['cart_id']);

        $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
        $stmt->bind_param("ii", $cartId, $userId);
        $stmt->execute();
        $stmt->close();
    }
} else {
    // Если пользователь не авторизован (гость)
    if ($action === 'update' && isset($_POST['cart_id'], $_POST['quantity'])) {
        $cartId = intval($_POST['cart_id']);
        $quantity = max(1, intval($_POST['quantity'])); // Минимум 1

        if (isset($_SESSION['cart'][$cartId])) {
            $_SESSION['cart'][$cartId] = $quantity;
        }
    } elseif ($action === 'delete' && isset($_POST['cart_id'])) {
        $cartId = intval($_POST['cart_id']);

        if (isset($_SESSION['cart'][$cartId])) {
            unset($_SESSION['cart'][$cartId]);
        }
    }
}

// Перенаправляем обратно на страницу корзины
header("Location: cart.php");
exit;