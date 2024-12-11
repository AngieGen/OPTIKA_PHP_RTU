<?php
session_start();
include 'config.php';

if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cartId = intval($_POST['cart_id']);
    $quantity = max(1, intval($_POST['quantity'])); // Убедимся, что количество >= 1

    if (isset($_SESSION['user_id'])) {
        // Проверяем, принадлежит ли корзина текущему пользователю
        $userId = $_SESSION['user_id'];
        $checkStmt = $conn->prepare("SELECT id FROM cart WHERE id = ? AND user_id = ?");
        $checkStmt->bind_param("ii", $cartId, $userId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            // Обновляем количество
            $stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
            $stmt->bind_param("ii", $quantity, $cartId);
            if ($stmt->execute()) {
                $_SESSION['message'] = "Grozs veiksmīgi atjaunināts.";
            } else {
                $_SESSION['message'] = "Kļūda, atjauninot grozu.";
            }
            $stmt->close();
        } else {
            $_SESSION['message'] = "Kļūda: Nevar atjaunināt svešu grozu.";
        }
        $checkStmt->close();
    } else {
        // Обновление корзины в сессии для гостей
        if (isset($_SESSION['cart'][$cartId])) {
            $_SESSION['cart'][$cartId] = $quantity;
            $_SESSION['message'] = "Grozs veiksmīgi atjaunināts.";
        } else {
            $_SESSION['message'] = "Kļūda: Produkts nav atrasts grozā.";
        }
    }
}

header("Location: cart.php");
exit;