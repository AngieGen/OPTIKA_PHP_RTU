<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "optika";

// Подключение к базе данных
$conn = new mysqli($servername, $username, $password, $dbname);

// Проверка подключения
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// Установка кодировки
$conn->set_charset("utf8mb4");
?>