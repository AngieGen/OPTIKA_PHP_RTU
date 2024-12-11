<?php
session_start();
include 'config.php';

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Получение данных о товаре
$product_id = intval($_GET['id']);
$query = "SELECT * FROM myproducts WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = intval($_POST['category_id']);
    $subcategory_id = intval($_POST['subcategory_id']);
    $stock = intval($_POST['stock']);
    $imagePath = $product['image']; // Оставляем старый путь к изображению по умолчанию

    // Проверка загрузки нового изображения
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = floatval($_POST['price']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = intval($_POST['category_id']);
    $subcategory_id = intval($_POST['subcategory_id']);
    $stock = intval($_POST['stock']);
    $imagePath = $product['image']; // Оставляем старый путь к изображению по умолчанию

    // Проверка загрузки нового изображения
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = __DIR__ . '/images/products/';
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetFilePath = $uploadDir . $fileName;

        // Проверка существования папки
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Проверка типа файла
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                // Удаляем старое изображение, если это не дефолтное
                if ($product['image'] !== 'images/products/default.jpg' && file_exists(__DIR__ . '/' . $product['image'])) {
                    unlink(__DIR__ . '/' . $product['image']);
                }

                $imagePath = 'images/products/' . $fileName; // Новый путь для базы данных
            } else {
                $error = "Kļūda, augšupielādējot attēlu.";
            }
        } else {
            $error = "Nepareizs attēla formāts. Atļautie formāti: JPG, JPEG, PNG, GIF.";
        }
    }

    // Если ошибок нет, обновляем запись
    if (!isset($error)) {
        $query = "UPDATE myproducts SET name = ?, price = ?, description = ?, category_id = ?, subcategory_id = ?, stock = ?, image = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sdssiisi", $name, $price, $description, $category_id, $subcategory_id, $stock, $imagePath, $product_id);

        if ($stmt->execute()) {
            header("Location: admin_products.php");
            exit;
        } else {
            $error = "Kļūda, atjauninot produktu: " . $stmt->error;
        }
    }
}
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rediģēt produktu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <div class="page-container">
        <div class="content">
            <div class="container edit-product">
                <h1 class="page-title">Rediģēt produktu</h1>

                <!-- Сообщение об ошибке -->
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>

                <!-- Форма редактирования -->
                <form method="POST" action="" enctype="multipart/form-data" class="product-form">
                    <div class="form-group">
                        <label for="name">Nosaukums</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="price">Cena (€)</label>
                        <input type="number" step="0.01" id="price" name="price" class="form-control" value="<?php echo htmlspecialchars($product['price']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="description">Apraksts</label>
                        <textarea id="description" name="description" class="form-control" rows="3" required><?php echo htmlspecialchars($product['description']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="stock">Skaits noliktavā</label>
                        <input type="number" id="stock" name="stock" class="form-control" value="<?php echo htmlspecialchars($product['stock']); ?>" min="0" required>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Kategorija</label>
                        <select id="category_id" name="category_id" class="form-select" required>
                            <option value="1" <?php echo $product['category_id'] == 1 ? 'selected' : ''; ?>>Brilles</option>
                            <option value="2" <?php echo $product['category_id'] == 2 ? 'selected' : ''; ?>>Saulesbrilles</option>
                            <option value="3" <?php echo $product['category_id'] == 3 ? 'selected' : ''; ?>>Kontaktlēcas</option>
                            <option value="4" <?php echo $product['category_id'] == 4 ? 'selected' : ''; ?>>Aksesuāri</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="subcategory_id">Subkategorija</label>
                        <select id="subcategory_id" name="subcategory_id" class="form-select" required>
                            <option value="1" <?php echo $product['subcategory_id'] == 1 ? 'selected' : ''; ?>>Sieviešu</option>
                            <option value="2" <?php echo $product['subcategory_id'] == 2 ? 'selected' : ''; ?>>Vīriešu</option>
                            <option value="3" <?php echo $product['subcategory_id'] == 3 ? 'selected' : ''; ?>>Bērnu</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="current-image">Esošais attēls</label>
                        <div class="current-image">
                            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="Produkta attēls">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="image">Augšupielādēt jaunu attēlu</label>
                        <input type="file" id="image" name="image" class="form-control" accept="image/*">
                    </div>

                    <button type="submit" class="btn btn-primary">Saglabāt</button>
                </form>
            </div>
        </div>
        <!-- Футер -->
        <?php require_once "footer.php"; ?>
    </div>
</body>
</html>