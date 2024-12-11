<?php
session_start();

if (!isset($_SESSION['message'])) {
    header("Location: index.php");
    exit;
}
$message = $_SESSION['message'];
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <title>Pasūtījuma Apstiprinājums</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php require_once "header.php"; ?>
    <div class="container mt-5 text-center">
        <h1>Pasūtījuma Apstiprinājums</h1>
        <p class="alert alert-success"><?php echo htmlspecialchars($message); ?></p>
        <a href="index.php" class="btn btn-primary">Atpakaļ uz Sākumlapu</a>
    </div>
    <?php require_once "footer.php"; ?>
</body>
</html>