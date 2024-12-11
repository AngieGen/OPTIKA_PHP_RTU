<?php
// Подключение к базе данных
include 'config.php';

// Переменные для запросов
$result = null;
$orderBy = "";



// Проверяем, какой фильтр выбран
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'name_asc':
            $orderBy = "ORDER BY name ASC";
            break;
        case 'name_desc':
            $orderBy = "ORDER BY name DESC";
            break;
        case 'price_high':
            $orderBy = "ORDER BY price DESC";
            break;
        case 'price_low':
            $orderBy = "ORDER BY price ASC";
            break;
        case 'newest':
            $orderBy = "ORDER BY created_at DESC";
            break;
        default: 
            $orderBy = "ORDER BY stock DESC"; // Популярные
            break;
    }
}

// Проверяем, был ли передан параметр search
if (isset($_GET['search']) && !empty($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT * FROM myproducts WHERE name LIKE '%$search%' OR description LIKE '%$search%' $orderBy";
} elseif (isset($_GET['subcategory_id']) && is_numeric($_GET['subcategory_id'])) {
    // Фильтр по подкатегории
    $subcategoryId = intval($_GET['subcategory_id']);
    $query = "SELECT * FROM myproducts WHERE subcategory_id = $subcategoryId $orderBy";
} elseif (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    // Фильтр по категории
    $categoryId = intval($_GET['category_id']);
    $query = "SELECT * FROM myproducts WHERE category_id = $categoryId $orderBy";
} else {
    header("Location: index.php");
    exit;
}

// Выполняем запрос
$result = mysqli_query($conn, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Produkti</title>
    
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <!-- Фильтр -->
    <div class="container my-4">
        <form method="GET" action="products.php" class="d-flex justify-content-between align-items-center">
            <!-- Скрытые поля для передачи параметров category_id или subcategory_id -->
            <?php if (isset($_GET['category_id'])) { ?>
                <input type="hidden" name="category_id" value="<?php echo intval($_GET['category_id']); ?>">
            <?php } ?>
            <?php if (isset($_GET['subcategory_id'])) { ?>
                <input type="hidden" name="subcategory_id" value="<?php echo intval($_GET['subcategory_id']); ?>">
            <?php } ?>

            <!-- Фильтр -->
            <select class="form-select me-2" name="sort" aria-label="Filter">
                <option value="popular" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'popular') ? 'selected' : ''; ?>>Populārākās</option>
                <option value="name_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Nosaukums A līdz Z</option>
                <option value="name_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'selected' : ''; ?>>Nosaukums Z līdz A</option>
                <option value="price_high" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_high') ? 'selected' : ''; ?>>Cenas, sākot no augstākās</option>
                <option value="price_low" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_low') ? 'selected' : ''; ?>>Cenas, sākot no zemākās</option>
                <option value="newest" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Jaunākās</option>
            </select>
            <button class="btn btn-primary" type="submit">Filtrēt</button>
        </form>
    </div>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert alert-success text-center">
            <?php 
            echo $_SESSION['message'];
            unset($_SESSION['message']); // Удаляем сообщение после отображения
             ?>
         </div>
    <?php endif; ?>

    <!-- Список продуктов -->
    <div class="container">
        <h1 class="text-center mb-4">Produkti</h1>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php if (mysqli_num_rows($result) > 0) { ?>
                <?php while ($product = mysqli_fetch_assoc($result)) { ?>
                    <div class="col">
                        <div class="card h-100">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" class="card-img-top product-image" alt="<?php echo htmlspecialchars($product['name']); ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($product['description']); ?></p>
                                <p class="card-text"><strong>€ <?php echo htmlspecialchars($product['price']); ?></strong></p>
                                <!-- Форма для добавления в корзину -->
                            <form method="POST" action="add_to_cart.php">
                              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                              <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                              <button type="submit" class="btn btn-warning">Pievienot grozam</button>
                            </form>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p class="text-center">Nav pieejami produkti šajā kategorijā.</p>
            <?php } ?>
        </div>
    </div>

    <?php require_once "footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>