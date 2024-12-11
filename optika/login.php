<?php
session_start();
include 'config.php';

// Если пользователь уже авторизован, перенаправляем его на главную
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Проверка формы входа
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Проверяем, существует ли пользователь с таким email
    $query = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Проверяем пароль
        if (password_verify($password, $user['password'])) {
            // Сохраняем данные в сессию
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name']; // Сохраняем имя пользователя
            $_SESSION['role'] = $user['role'];

            // Перенаправление в зависимости от роли
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } else {
                header("Location: index.php");
            }
            exit;
        } else {
            $error = "Nepareizs parole.";
        }
    } else {
        $error = "Lietotājs ar norādīto e-pastu netika atrasts.";
    }
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pieslēgties</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <div class="page-container">
        <div class="content">
            <div class="form-container">
                <h2 class="form-title">Pieslēgties</h2>

                <!-- Сообщение об ошибке -->
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>

                <form method="POST" action="" class="form">
                    <!-- Поле E-pasts -->
                     <label for="email" class="form-label">E-pasts</label>
                    <div class="form-group">
                        
                        <input type="email" class="form-input" id="email" name="email" placeholder="Ievadiet e-pastu" required>
                    </div>

                    <!-- Поле Parole -->
                     <label for="password" class="form-label">Parole</label>
                    <div class="form-group">
                        
                        <input type="password" class="form-input" id="password" name="password" placeholder="Ievadiet paroli" required>
                    </div>

                    <!-- Кнопка входа -->
                    <button type="submit" class="btn btn-primary">Ieiet</button>
                </form>

                <!-- Ссылка на регистрацию -->
                <p class="form-footer">
                    Nav konta? <a href="register.php" class="form-link">Reģistrēties</a>
                </p>
            </div>
        </div>

        <!-- Футер -->
        <?php require_once "footer.php"; ?>
    </div>
</body>
</html>

<!-- Футер -->
   <?php require_once "footer.php"; ?>
   
</body>
</html>