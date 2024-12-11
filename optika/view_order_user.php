<?php
session_start();
include 'config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id']) || !isset($_GET['id'])) {
    header("Location: account.php");
    exit;
}

$orderId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

// Проверка, принадлежит ли заказ пользователю
$query = "SELECT id, total_price, order_date, status, address, payment_method 
          FROM orders 
          WHERE id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $orderId, $userId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    header("Location: account.php");
    exit;
}

// Получение товаров в заказе
$itemQuery = "SELECT myproducts.name, order_items.quantity, order_items.price 
              FROM order_items 
              JOIN myproducts ON order_items.product_id = myproducts.id 
              WHERE order_items.order_id = ?";
$itemStmt = $conn->prepare($itemQuery);
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$orderItems = $itemStmt->get_result();
$itemStmt->close();
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pasūtījuma detaļas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Pasūtījuma detaļas</h1>
        <p><strong>Pasūtījuma ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
        <p><strong>Datums:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
        <p><strong>Kopējā summa:</strong> €<?php echo htmlspecialchars($order['total_price']); ?></p>
        <p><strong>Statuss:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
        <p><strong>Adrese:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
        <p><strong>Maksāšanas metode:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>

        <h2>Produkti</h2>
        <table class="table">
            <thead>
                <tr>
                    <th>Nosaukums</th>
                    <th>Daudzums</th>
                    <th>Cena (€)</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $orderItems->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['name']); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>€<?php echo htmlspecialchars($item['price']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>