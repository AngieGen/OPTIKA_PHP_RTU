<?php
session_start();
include 'config.php';

if (!isset($_SESSION['quiz_answers'])) {
    header("Location: quiz.php");
    exit;
}

$answers = $_SESSION['quiz_answers'];
$style = $answers['style'];
$brand = $answers['brand'];
$purpose = $answers['purpose'];

// Формирование SQL-запроса
$query = "SELECT * FROM myproducts WHERE 1=1";
$params = [];
$types = "";

// Фильтр по категории (стиль)
if ($style) {
    if ($style == 'vision') {
        $query .= " AND category_id = ?";
        $params[] = 1; // ID категории "Очки"
        $types .= "i";
    } elseif ($style == 'sunglasses') {
        $query .= " AND category_id = ?";
        $params[] = 2; // ID категории "Солнечные очки"
        $types .= "i";
    }
}

// Фильтр по бренду
if ($brand) {
    $query .= " AND name LIKE ?";
    $params[] = "%$brand%";
    $types .= "s";
}

// Подготовка запроса
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$results = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rezultāti</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <!-- Контейнер для результатов -->
    <div class="container mt-5">
        <h1 class="text-center page-title">Jūsu rezultāti</h1>
        <p class="text-center">Šeit ir produkti, kas atbilst Jūsu izvēlei.</p>

        <?php if ($results->num_rows > 0): ?>
            <div class="row">
                <?php while ($product = $results->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card product-card">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" class="card-img-top product-image">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text">
                                    Cena: <strong>€<?php echo htmlspecialchars($product['price']); ?></strong>
                                </p>
                                <!-- Форма для добавления в корзину -->
                            <form method="POST" action="add_to_cart.php">
                              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                              <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                              <button type="submit" class="btn btn-warning">Pievienot grozam</button>
                            </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-warning text-center">
                <p>Diemžēl neatradām produktus, kas atbilst Jūsu izvēlei.</p>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="quiz.php" class="btn btn-secondary">Sākt no jauna</a>
        </div>
    </div>

    <!-- Нижнее меню -->
    <?php require_once "footer.php"; ?>
</body>
</html>