<?php
session_start();
include 'config.php';

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

// Получение заказов пользователя
$query = "SELECT id, total_price, order_date, status 
          FROM orders 
          WHERE user_id = ? 
          ORDER BY order_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$orders = $stmt->get_result();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Mani pasūtījumi</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="container">
        <h1>Mani pasūtījumi</h1>
        <?php if ($orders->num_rows > 0): ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Pasūtījuma ID</th>
                        <th>Datums</th>
                        <th>Kopējā summa (€)</th>
                        <th>Statuss</th>
                        <th>Detaļas</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($order = $orders->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($order['id']); ?></td>
                            <td><?php echo htmlspecialchars($order['order_date']); ?></td>
                            <td><?php echo htmlspecialchars($order['total_price']); ?></td>
                            <td><?php echo htmlspecialchars($order['status']); ?></td>
                            <td><a href="view_order_user.php?id=<?php echo $order['id']; ?>">Apskatīt</a></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nav atrasti pasūtījumi.</p>
        <?php endif; ?>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>