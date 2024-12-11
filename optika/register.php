<?php
session_start();
include 'config.php';

// Если пользователь уже авторизован, перенаправляем на главную страницу
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

// Проверка отправки формы
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm_password']);

    // Проверяем, совпадают ли пароли
    if ($password !== $confirmPassword) {
        $error = "Paroles nesakrīt.";
    } else {
        // Проверяем, существует ли пользователь с таким email
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Lietotājs ar šo e-pastu jau ir reģistrēts.";
        } else {
            // Хешируем пароль
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            // Добавляем пользователя в базу данных
            $query = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'registered_user')";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $name, $email, $hashedPassword);

            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['role'] = 'registered_user';

                // Перенаправляем на главную страницу после успешной регистрации
                header("Location: index.php");
                exit;
            } else {
                $error = "Reģistrācija neizdevās. Lūdzu, mēģiniet vēlreiz.";
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
    <title>Reģistrēties</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <div class="page-container">
        <div class="content">
            <div class="form-container">
                <h2 class="form-title">Reģistrēties</h2>

                <!-- Сообщение об ошибке -->
                <?php if (isset($error)) { ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php } ?>

                <form method="POST" action="" class="form">
                    <!-- Поле Name -->
                     <label for="name" class="form-label">Vārds</label>
                    <div class="form-group">
                        
                        <input type="text" class="form-input" id="name" name="name" placeholder="Ievadiet savu vārdu" required>
                    </div>

                    <!-- Поле Email -->
                     <label for="email" class="form-label">E-pasts</label>
                    <div class="form-group">
                        
                        <input type="email" class="form-input" id="email" name="email" placeholder="Ievadiet savu e-pastu" required>
                    </div>

                    <!-- Поле Password -->
                     <label for="password" class="form-label">Parole</label>
                    <div class="form-group">
                        
                        <input type="password" class="form-input" id="password" name="password" placeholder="Izveidojiet paroli" required>
                    </div>

                    <!-- Поле Confirm Password -->
                     <label for="confirm_password" class="form-label">Apstiprināt paroli</label>
                    <div class="form-group">
                        
                        <input type="password" class="form-input" id="confirm_password" name="confirm_password" placeholder="Atkārtojiet paroli" required>
                    </div>

                    <!-- Кнопка регистрации -->
                    <button type="submit" class="btn btn-primary ">Reģistrēties</button>
                </form>

                <p class="form-footer">
                    Jau reģistrējies? <a href="login.php" class="form-link">Ieiet</a>
                </p>
            </div>
        </div>

        <!-- Футер -->
        <?php require_once "footer.php"; ?>
    </div>
</body>
</html>