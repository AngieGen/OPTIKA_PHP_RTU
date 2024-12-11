<?php
session_start();
include 'config.php';

$cartItems = [];
$totalPrice = 0;

// Проверка корзины
if ((!isset($_SESSION['user_id']) && !isset($_SESSION['cart'])) || (isset($_SESSION['cart']) && empty($_SESSION['cart']))) {
    header("Location: cart.php");
    exit;
}

try {
    if (isset($_SESSION['user_id'])) {
        $userId = $_SESSION['user_id'];

        // Получаем товары для авторизованного пользователя
        $stmt = $conn->prepare("SELECT myproducts.name, myproducts.price, cart.quantity 
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
        // Получаем товары для гостей
        $productIds = array_keys($_SESSION['cart']);
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));

        $stmt = $conn->prepare("SELECT id, name, price FROM myproducts WHERE id IN ($placeholders)");
        $stmt->bind_param(str_repeat('i', count($productIds)), ...$productIds);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $row['quantity'] = $_SESSION['cart'][$row['id']];
            $cartItems[] = $row;
            $totalPrice += $row['price'] * $row['quantity'];
        }
        $stmt->close();
    }
} catch (Exception $e) {
    // Если произошла ошибка, перенаправляем в корзину с сообщением
    $_SESSION['cart_message'] = "Kļūda apstrādājot grozu: " . $e->getMessage();
    header("Location: cart.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apstiprināt Pasūtījumu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once "header.php"; ?>

    <div class="checkout-container">
        <div class="checkout-box">
            <h1 class="page-title">Apstiprināt Pasūtījumu</h1>
            <p class="checkout-summary">Kopējā summa: <strong>€ <?php echo number_format($totalPrice, 2); ?></strong></p>

            <form action="submit_order.php" method="POST" class="checkout-form">
                <div class="form-group">
                    <label for="address" class="form-label">Piegādes Adrese:</label>
                    <input type="text" id="address" name="address" class="form-input" placeholder="Ievadiet adresi" required>
                </div>
                <div class="form-group">
                    <label for="payment_method" class="form-label">Maksāšanas Metode:</label>
                    <select id="payment_method" name="payment_method" class="form-input" required>
                        <option value="card">Maksājumu Karte</option>
                        <option value="cash">Skaidrā Naudā</option>
                    </select>
                </div>
                <button type="submit" class="btn-primary">Apstiprināt</button>
            </form>
        </div>
    </div>

    <?php require_once "footer.php"; ?>
</body>
</html>