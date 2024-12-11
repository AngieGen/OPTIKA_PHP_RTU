<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$product_id = intval($_GET['id']);
$query = "DELETE FROM myproducts WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $product_id);

if ($stmt->execute()) {
    $_SESSION['message'] = "Produkts veiksmīgi dzēsts.";
} else {
    $_SESSION['message'] = "Kļūda, dzēšot produktu.";
}

header("Location: admin_products.php");
exit;
?>