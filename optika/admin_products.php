<?php
session_start();
include 'config.php';

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Получаем список категорий и субкатегорий для фильтра
$categories = mysqli_query($conn, "SELECT id, name FROM category");
$subcategories = mysqli_query($conn, "SELECT id, name FROM subcategory");

// Формирование основного запроса с фильтрацией
$where = [];
if (isset($_GET['category_id']) && is_numeric($_GET['category_id'])) {
    $categoryId = intval($_GET['category_id']);
    $where[] = "myproducts.category_id = $categoryId";
}
if (isset($_GET['subcategory_id']) && is_numeric($_GET['subcategory_id'])) {
    $subcategoryId = intval($_GET['subcategory_id']);
    $where[] = "myproducts.subcategory_id = $subcategoryId";
}
$whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

$query = "SELECT 
            myproducts.id, 
            myproducts.name, 
            myproducts.price, 
            category.name AS category_name, 
            subcategory.name AS subcategory_name 
          FROM myproducts
          LEFT JOIN category ON myproducts.category_id = category.id
          LEFT JOIN subcategory ON myproducts.subcategory_id = subcategory.id
          $whereClause";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pārvaldīt produktus</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <div class="page-container">
        <div class="content">
            <div class="container admin-products">
                <h1 class="page-title">Produktu pārvaldīšana</h1>
                <a href="add_product.php" class="btn btn-success mb-4">Pievienot jaunu produktu</a>

                <!-- Форма фильтрации -->
                <form method="GET" action="" class="filter-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="category_id">Kategorija</label>
                            <select name="category_id" id="category_id" class="form-select">
                                <option value="">Visas kategorijas</option>
                                <?php while ($category = mysqli_fetch_assoc($categories)) { ?>
                                    <option value="<?php echo $category['id']; ?>" 
                                        <?php echo (isset($_GET['category_id']) && $_GET['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="subcategory_id">Subkategorija</label>
                            <select name="subcategory_id" id="subcategory_id" class="form-select">
                                <option value="">Visas subkategorijas</option>
                                <?php while ($subcategory = mysqli_fetch_assoc($subcategories)) { ?>
                                    <option value="<?php echo $subcategory['id']; ?>" 
                                        <?php echo (isset($_GET['subcategory_id']) && $_GET['subcategory_id'] == $subcategory['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($subcategory['name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Filtrēt</button>
                            <a href="admin_products.php" class="btn btn-secondary">Notīrīt</a>
                        </div>
                    </div>
                </form>

                <!-- Таблица продуктов -->
                <div class="table-wrapper">
                    <table class="custom-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nosaukums</th>
                                <th>Cena</th>
                                <th>Kategorija</th>
                                <th>Subkategorija</th>
                                <th>Darbības</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($product = mysqli_fetch_assoc($result)) { ?>
                                <tr>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo htmlspecialchars($product['name']); ?></td>
                                    <td>€ <?php echo htmlspecialchars($product['price']); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? 'Nav kategorijas'); ?></td>
                                    <td><?php echo htmlspecialchars($product['subcategory_name'] ?? 'Nav subkategorijas'); ?></td>
                                    <td>
                                        <a href="edit_product.php?id=<?php echo $product['id']; ?>" class="btn btn-primary btn-sm">Rediģēt</a>
                                        <a href="delete_product.php?id=<?php echo $product['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Vai tiešām vēlaties dzēst produktu?');">Dzēst</a>
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