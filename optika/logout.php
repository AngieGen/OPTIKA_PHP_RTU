<?php
session_start();

// Удаление всех переменных сессии
$_SESSION = [];

// Уничтожение сессии
session_destroy();

// Перенаправление на страницу входа или главную страницу
header("Location: index.php");
exit;
?>