<?php
session_start();
include 'config.php';

if ((!isset($_SESSION['user_id']) && !isset($_SESSION['cart'])) || empty($_POST['address']) || empty($_POST['payment_method'])) {
    header("Location: cart.php");
    exit;
}

$address = mysqli_real_escape_string($conn, $_POST['address']);
$paymentMethod = mysqli_real_escape_string($conn, $_POST['payment_method']);
$totalPrice = 0;
$userId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
$cartItems = [];

// Получаем товары из корзины
if ($userId) {
    $stmt = $conn->prepare("SELECT cart.product_id, cart.quantity, myproducts.price 
                            FROM cart 
                            JOIN myproducts ON cart.product_id = myproducts.id 
                            WHERE cart.user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $totalPrice += $row['price'] * $row['quantity'];
    }
    $stmt->close();
} elseif (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $stmt = $conn->prepare("SELECT price FROM myproducts WHERE id = ?");
        $stmt->bind_param("i", $productId);
        $stmt->execute();
        $stmt->bind_result($price);
        $stmt->fetch();
        $cartItems[] = ['product_id' => $productId, 'quantity' => $quantity, 'price' => $price];
        $totalPrice += $price * $quantity;
        $stmt->close();
    }
}

// Сохраняем заказ
$stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, address, payment_method, order_date, status) 
                        VALUES (?, ?, ?, ?, NOW(), 'Apstrādē')");
$stmt->bind_param("idss", $userId, $totalPrice, $address, $paymentMethod);
$stmt->execute();
$orderId = $stmt->insert_id;
$stmt->close();

// Сохраняем товары заказа
$stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
foreach ($cartItems as $item) {
    $stmt->bind_param("iiid", $orderId, $item['product_id'], $item['quantity'], $item['price']);
    $stmt->execute();
}
$stmt->close();

// Очищаем корзину
if ($userId) {
    $stmt = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $stmt->close();
} else {
    unset($_SESSION['cart']);
}

$conn->close();

$_SESSION['message'] = "Pasūtījums veiksmīgi noformēts! Jūsu pasūtījuma numurs ir #" . $orderId;
header("Location: order_confirmation.php");
exit;
?>