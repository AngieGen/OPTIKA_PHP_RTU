<?php
session_start();

// Проверка данных
if (!isset($_POST['product_id']) || !isset($_POST['quantity']) || !is_numeric($_POST['quantity'])) {
    $_SESSION['cart_message'] = "Nepareizi dati. Produkts netika pievienots grozam.";
    header("Location: " . $_SERVER['HTTP_REFERER']);
    exit;
}

$productId = intval($_POST['product_id']);
$quantity = max(1, intval($_POST['quantity'])); // Минимум 1

try {
    // Для авторизованных пользователей
    if (isset($_SESSION['user_id'])) {
        include 'config.php';
        $userId = $_SESSION['user_id'];

        $stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?) 
                                ON DUPLICATE KEY UPDATE quantity = quantity + ?");
        $stmt->bind_param("iiii", $userId, $productId, $quantity, $quantity);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    } else {
        // Для гостей
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId] += $quantity;
        } else {
            $_SESSION['cart'][$productId] = $quantity;
        }
    }

    $_SESSION['cart_message'] = "Produkts veiksmīgi pievienots grozam!";
} catch (Exception $e) {
    $_SESSION['cart_message'] = "Kļūda pievienojot produktu.";
}

// Перенаправляем обратно
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;