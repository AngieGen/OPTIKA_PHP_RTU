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

// Получаем информацию о заказе (включая заказы гостей)
$query = "SELECT orders.id, 
                 users.name AS user_name, 
                 users.email AS user_email, 
                 orders.total_price, 
                 orders.address, 
                 orders.payment_method, 
                 orders.status, 
                 orders.order_date 
          FROM orders
          LEFT JOIN users ON orders.user_id = users.id
          WHERE orders.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

// Получаем товары из заказа
$itemQuery = "SELECT myproducts.name, order_items.quantity, myproducts.price, (order_items.quantity * myproducts.price) AS total
              FROM order_items
              JOIN myproducts ON order_items.product_id = myproducts.id
              WHERE order_items.order_id = ?";
$itemStmt = $conn->prepare($itemQuery);
$itemStmt->bind_param("i", $orderId);
$itemStmt->execute();
$orderItems = $itemStmt->get_result();

$stmt->close();
$itemStmt->close();
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasūtījuma informācija</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <div class="page-container">
        <div class="content">
            <div class="container order-info">
                <h1 class="page-title">Pasūtījuma informācija</h1>

                <?php if ($order) { ?>
                <!-- Информация о заказе -->
                <div class="order-details">
                    <p><strong>Pasūtījuma ID:</strong> <?php echo $order['id']; ?></p>
                    <p><strong>Lietotājs:</strong> <?php echo htmlspecialchars($order['user_name'] ?? 'Viesis'); ?></p>
                    <p><strong>E-pasts:</strong> <?php echo htmlspecialchars($order['user_email'] ?? 'Nav pieejams'); ?></p>
                    <p><strong>Kopējā cena:</strong> € <?php echo htmlspecialchars($order['total_price']); ?></p>
                    <p><strong>Adrese:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
                    <p><strong>Maksājuma metode:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
                    <p><strong>Statuss:</strong> <?php echo htmlspecialchars($order['status']); ?></p>
                    <p><strong>Izveidots:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
                </div>

                <!-- Таблица с продуктами -->
                <h2 class="section-title">Produkti</h2>
                <div class="table-wrapper">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>Nosaukums</th>
                                <th>Daudzums</th>
                                <th>Cena</th>
                                <th>Kopā</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($item = $orderItems->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td>€ <?php echo htmlspecialchars($item['price']); ?></td>
                                    <td>€ <?php echo htmlspecialchars($item['total']); ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>

                <!-- Форма изменения статуса -->
                <h2 class="section-title">Mainīt pasūtījuma statusu</h2>
                <form method="POST" action="update_order_status.php" class="status-form">
                    <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                    <div class="form-group">
                        <label for="status">Pasūtījuma statuss:</label>
                        <select name="status" id="status" class="form-select">
                            <option value="Pabeigts" <?php echo $order['status'] == 'Pabeigts' ? 'selected' : ''; ?>>Pabeigts</option>
                            <option value="Apstrādē" <?php echo $order['status'] == 'Apstrādē' ? 'selected' : ''; ?>>Apstrādē</option>
                            <option value="Atcelts" <?php echo $order['status'] == 'Atcelts' ? 'selected' : ''; ?>>Atcelts</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Atjaunināt statusu</button>
                </form>
                <?php } else { ?>
                    <p class="alert alert-warning">Pasūtījums netika atrasts.</p>
                <?php } ?>
            </div>
        </div>
        <!-- Футер -->
        <?php require_once "footer.php"; ?>
    </div>
</body>
</html>