<?php
session_start();
include 'config.php';

// Проверяем, администратор ли пользователь
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Получаем все заказы, включая гостей
$query = "SELECT 
            orders.id, 
            users.name AS user_name, 
            users.email AS user_email, 
            orders.total_price, 
            orders.status, 
            orders.order_date 
          FROM orders
          LEFT JOIN users ON orders.user_id = users.id 
          ORDER BY orders.order_date DESC";
$result = mysqli_query($conn, $query);
?>
   
<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pasūtījumi</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <div class="page-container">
        <div class="content">
            <div class="container">
                <h1 class="page-title">Pasūtījumu saraksts</h1>
                <div class="table-wrapper">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Lietotājs</th>
                                <th>E-pasts</th>
                                <th>Kopā</th>
                                <th>Statuss</th>
                                <th>Izveidots</th>
                                <th>Darbības</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo htmlspecialchars($row['user_name'] ?? 'Viesis'); ?></td>
                                    <td><?php echo htmlspecialchars($row['user_email'] ?? 'Nav pieejams'); ?></td>
                                    <td>€ <?php echo htmlspecialchars($row['total_price']); ?></td>
                                    <td>
                                        <form method="POST" action="update_order_status.php" class="status-form">
                                            <input type="hidden" name="order_id" value="<?php echo $row['id']; ?>">
                                            <select name="status" class="status-select">
                                                <option value="Pabeigts" <?php echo $row['status'] == 'Pabeigts' ? 'selected' : ''; ?>>Pabeigts</option>
                                                <option value="Apstrādē" <?php echo $row['status'] == 'Apstrādē' ? 'selected' : ''; ?>>Apstrādē</option>
                                                <option value="Atcelts" <?php echo $row['status'] == 'Atcelts' ? 'selected' : ''; ?>>Atcelts</option>
                                            </select>
                                            <button type="submit" class="btn btn-save">Saglabāt</button>
                                        </form>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['order_date']); ?></td>
                                    <td class="actions">
                                        <a href="view_order.php?id=<?php echo $row['id']; ?>" class="btn btn-view">Skatīt</a>
                                        <a href="delete_order.php?id=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Vai tiešām vēlaties dzēst šo pasūtījumu?');">Dzēst</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- Футер -->
        <?php require_once "footer.php"; ?>
    </div>
</body>
</html>