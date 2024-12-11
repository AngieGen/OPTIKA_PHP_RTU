<?php
session_start();
include 'config.php';

// Проверяем, авторизован ли пользователь и является ли он администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrācijas Panelis</title>
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
      rel="stylesheet"
    />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <div class="container my-4">
        <h1 class="text-center">Administrācijas Panelis</h1>
        <div class="row mt-4">
            <!-- Заказы -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Pasūtījumi</h5>
                        <p class="card-text">Apskatīt un apstrādāt pasūtījumus.</p>
                        <a href="admin_orders.php" class="btn btn-primary">Pārvaldīt</a>
                    </div>
                </div>
            </div>

            <!-- Товары -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Produkti</h5>
                        <p class="card-text">Pievienot, rediģēt un dzēst produktus.</p>
                        <a href="admin_products.php" class="btn btn-primary">Pārvaldīt</a>
                    </div>
                </div>
            </div>

            <!-- Пользователи -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Lietotāji</h5>
                        <p class="card-text">Apskatīt un pārvaldīt lietotāju kontus.</p>
                        <a href="admin_users.php" class="btn btn-primary">Pārvaldīt</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Футер -->
    <?php require_once "footer.php"; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>