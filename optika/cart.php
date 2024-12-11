<?php
session_start();
include 'config.php';

$cartItems = [];
$totalPrice = 0;

// Проверка авторизации
if (isset($_SESSION['user_id'])) {
    $userId = $_SESSION['user_id'];

    // Получаем товары из базы данных для авторизованного пользователя
    $stmt = $conn->prepare("SELECT cart.id, myproducts.name, myproducts.price, cart.quantity, (myproducts.price * cart.quantity) AS total
                            FROM cart
                            JOIN myproducts ON cart.product_id = myproducts.id
                            WHERE cart.user_id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
        $totalPrice += $row['total'];
    }

    $stmt->close();
} else {
    // Получаем товары из сессионной корзины для гостей
    if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
        $productIds = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        // Формируем запрос к базе данных
        $stmt = $conn->prepare("SELECT * FROM myproducts WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($productIds)), ...$productIds);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = $_SESSION['cart'][$row['id']];
            $row['total'] = $row['price'] * $row['quantity'];
            $cartItems[] = $row;
            $totalPrice += $row['total'];
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Grozs</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="page-container">
        <!-- Верхнее меню -->
        <?php require_once "header.php"; ?>

        <div class="cart-container">
            <h1 class="cart-title">Jūsu grozā</h1>
            <?php if (count($cartItems) > 0) { ?>
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Nosaukums</th>
                            <th>Cena</th>
                            <th>Daudzums</th>
                            <th>Kopā</th>
                            <th>Darbības</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item) { ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['name']); ?></td>
                                <td>€ <?php echo htmlspecialchars($item['price']); ?></td>
                                <td>
                                    <form method="POST" action="cart_actions.php" class="update-form">
                                        <input type="hidden" name="action" value="update">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <input type="number" name="quantity" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" class="quantity-input">
                                        <button type="submit" class="btn-update">Atjaunināt</button>
                                    </form>
                                </td>
                                <td>€ <?php echo htmlspecialchars($item['total']); ?></td>
                                <td>
                                    <form method="POST" action="cart_actions.php">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="cart_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" class="btn-delete">Dzēst</button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div class="cart-summary">
                    <p class="total">Kopā: <span>€ <?php echo number_format($totalPrice, 2); ?></span></p>
                    <form method="POST" action="checkout.php">
                        <button type="submit" class="btn-checkout">Pasūtīt</button>
                    </form>
                </div>
            <?php } else { ?>
                <p class="empty-cart">Jūsu grozs ir tukšs.</p>
            <?php } ?>
        </div>
        
        <!-- Футер -->
        <?php require_once "footer.php"; ?>
    </div>
</body>
</html>