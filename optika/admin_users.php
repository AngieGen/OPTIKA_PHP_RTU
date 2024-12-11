<?php
session_start();
include 'config.php';

// Проверяем, является ли пользователь администратором
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Получаем всех пользователей
$query = "SELECT id, name, email, role, created_at FROM users";
$result = mysqli_query($conn, $query);

// Проверяем наличие сообщения
$message = isset($_SESSION['message']) ? $_SESSION['message'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Удаляем сообщение из сессии после отображения
unset($_SESSION['message']);
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lietotāju pārvaldīšana</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <div class="container my-4">
        <h1>Lietotāju pārvaldīšana</h1>
        
        <!-- Сообщения -->
        <?php if ($message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <table class="table table-striped">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Vārds</th>
                    <th>E-pasts</th>
                    <th>Loma</th>
                    <th>Reģistrācijas datums</th>
                    <th>Darbības</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = mysqli_fetch_assoc($result)) { ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo htmlspecialchars($user['name']); ?></td>
                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                        <td>
                            <!-- Форма для изменения роли -->
                            <form method="POST" action="update_user_role.php" class="d-flex align-items-center">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <select name="role" class="form-select me-2">
                                    <option value="registered_user" <?php echo $user['role'] == 'registered_user' ? 'selected' : ''; ?>>Registered User</option>
                                    <option value="admin" <?php echo $user['role'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                </select>
                                <button type="submit" class="btn btn-primary btn-sm">Saglabāt</button>
                            </form>
                        </td>
                        <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                        <td>
                            <!-- Кнопка удаления пользователя -->
                            <form method="POST" action="delete_user.php" onsubmit="return confirm('Vai tiešām vēlaties dzēst šo lietotāju?');">
                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Dzēst</button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <!-- Футер -->
    <?php require_once "footer.php"; ?>
</body>
</html>