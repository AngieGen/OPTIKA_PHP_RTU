<?php
session_start();
include 'config.php';

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Получаем список категорий
$categoriesQuery = "SELECT * FROM category";
$categoriesResult = mysqli_query($conn, $categoriesQuery);

$subcategoriesQuery = "SELECT * FROM subcategory";
$subcategoriesResult = mysqli_query($conn, $subcategoriesQuery);

// Обработка формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT); // Проверяем, что значение является числом
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    $subcategory_id = intval($_POST['subcategory_id']);
    $imagePath = 'images/products/default.jpg'; // Умолчательный путь к изображению

    if ($price === false) {
        $error = "Cena nav derīga. Lūdzu, ievadiet pareizu cenu.";
    } elseif (mysqli_num_rows($categoriesResult) === 0 || mysqli_num_rows($subcategoriesResult) === 0) {
        $error = "Nav pieejamas kategorijas vai subkategorijas.";
    } else {
        // Обработка загрузки изображения
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/images/products/';
            $fileName = time() . '_' . basename($_FILES['image']['name']);
            $targetFilePath = $uploadDir . $fileName;

            // Проверка существования папки
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Проверка типа файла и размера
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            $fileSize = $_FILES['image']['size'];
            if (in_array($fileType, $allowedTypes) && $fileSize <= 5 * 1024 * 1024) { // Ограничение размера 5MB
                if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFilePath)) {
                    $imagePath = 'images/products/' . $fileName;
                } else {
                    $error = "Kļūda, augšupielādējot attēlu.";
                }
            } else {
                $error = "Nepareizs attēla formāts vai pārāk liels fails.";
            }
        }

        // Если ошибок нет, вставляем данные в базу
        if (!isset($error)) {
            $query = "INSERT INTO myproducts (name, description, price, stock, category_id, subcategory_id, image) 
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssdsiis", $name, $description, $price, $stock, $category_id, $subcategory_id, $imagePath);

            if ($stmt->execute()) {
                header("Location: admin_products.php");
                exit;
            } else {
                $error = "Kļūda, pievienojot produktu: " . $stmt->error;
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
    <title>Pievienot produktu</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        /* Основные стили страницы */
body {
    font-family: "Roboto", sans-serif;
    background-color: #fff5e6;
    color: #4a2c00;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 800px;
    margin: 20px auto;
    background: #ffffff;
    padding: 20px;
    border-radius: 8px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

/* Заголовок */
h1 {
    font-size: 2rem;
    color: #6c3b00;
    text-align: center;
    margin-bottom: 20px;
}

/* Формы */
form {
    margin-top: 10px;
}

.form-label {
    display: block;
    font-size: 1rem;
    font-weight: bold;
    margin-bottom: 5px;
    color: #6c3b00;
}

.form-control,
.form-select {
    width: 100%;
    padding: 10px;
    font-size: 1rem;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-sizing: border-box;
    margin-bottom: 15px;
}

.form-control:focus,
.form-select:focus {
    border-color: #6c3b00;
    outline: none;
    box-shadow: 0 0 4px rgba(108, 59, 0, 0.5);
}

/* Кнопка */
.btn-success {
    display: block;
    width: 100%;
    padding: 10px;
    font-size: 1.2rem;
    font-weight: bold;
    color: #fff;
    background-color: #4caf50;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    transition: background-color 0.3s ease;
}

.btn-success:hover {
    background-color: #388e3c;
}

/* Сообщения об ошибке */
.alert {
    padding: 10px;
    margin-bottom: 15px;
    border-radius: 5px;
    font-size: 1rem;
    text-align: center;
}

.alert-danger {
    background-color: #f8d7da;
    color: #842029;
    border: 1px solid #f5c2c7;
}

/* Медиа-запросы */
@media (max-width: 768px) {
    .container {
        padding: 15px;
    }

    h1 {
        font-size: 1.5rem;
    }

    .btn-success {
        font-size: 1rem;
    }
}
    </style>
</head>
<body>
    <?php require_once "header.php"; ?>
    <div class="container my-4">
        <h1>Pievienot jaunu produktu</h1>
        <?php if (isset($error)) { echo "<div class='alert alert-danger'>$error</div>"; } ?>
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="name" class="form-label">Nosaukums</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Cena (€)</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Apraksts</label>
                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="stock" class="form-label">Skaits noliktavā</label>
                <input type="number" class="form-control" id="stock" name="stock" min="0" required>
            </div>
            <div class="mb-3">
                <label for="category_id" class="form-label">Kategorija</label>
                <select class="form-select" id="category_id" name="category_id" required>
                    <?php while ($category = mysqli_fetch_assoc($categoriesResult)) { ?>
                        <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="subcategory_id" class="form-label">Subkategorija</label>
                <select class="form-select" id="subcategory_id" name="subcategory_id" required>
                    <?php while ($subcategory = mysqli_fetch_assoc($subcategoriesResult)) { ?>
                        <option value="<?php echo $subcategory['id']; ?>"><?php echo htmlspecialchars($subcategory['name']); ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="image" class="form-label">Augšupielādēt attēlu</label>
                <input type="file" class="form-control" id="image" name="image" accept="image/*">
            </div>
            <button type="submit" class="btn btn-success">Pievienot</button>
        </form>
    </div>
    <?php require_once "footer.php"; ?>
</body>
</html>