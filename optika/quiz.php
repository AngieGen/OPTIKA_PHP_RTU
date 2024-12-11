<?php
session_start();

// Если пользователь завершил опрос
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_quiz'])) {
    $_SESSION['quiz_answers'] = [
        'style' => $_POST['style'] ?? null,
        'brand' => $_POST['brand'] ?? null,
        'purpose' => $_POST['purpose'] ?? null,
    ];
    header("Location: quiz_results.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="lv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stila tests</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>

    <!-- Контейнер для теста -->
    <div class="container mt-5 quiz-container">
        <h1 class="text-center quiz-title">Stila tests</h1>
        <p class="text-center quiz-description">
            Aizpildiet mūsu stila testu un saņemiet personalizētus ieteikumus brillēm un saulesbrillēm!
        </p>

        <form method="POST" action="" class="quiz-form">
            <!-- Выбор стиля -->
            <div class="mb-4 quiz-question">
                <label for="style" class="form-label">Kāds ir Jūsu stils?</label>
                <select name="style" id="style" class="form-select" required>
                    <option value="">Izvēlieties stilu</option>
                    <option value="vision">Redzes</option>
                    <option value="sunglasses">Saulesbrilles</option>
                </select>
            </div>

            <!-- Выбор бренда -->
            <div class="mb-4 quiz-question">
                <label for="brand" class="form-label">Kurš zīmols Jums patīk?</label>
                <select name="brand" id="brand" class="form-select">
                    <option value="">Visi zīmoli</option>
                    <option value="Gucci">Gucci</option>
                    <option value="Prada">Prada</option>
                    <option value="Chloe">Chloe</option>
                    <option value="Boss">Boss</option>
                </select>
            </div>

            <!-- Цель использования -->
            <div class="mb-4 quiz-question">
                <label for="purpose" class="form-label">Kādam mērķim nepieciešamas brilles?</label>
                <select name="purpose" id="purpose" class="form-select">
                    <option value="">Visi mērķi</option>
                    <option value="fashion">Stilam</option>
                    <option value="reading">Lasīšanai</option>
                    <option value="sport">Sportam</option>
                </select>
            </div>

            <!-- Кнопка отправки -->
            <div class="text-center">
                <button type="submit" name="submit_quiz" class="btn btn-primary btn-lg">Iesniegt</button>
            </div>
        </form>
    </div>

    <!-- Нижнее меню -->
    <?php require_once "footer.php"; ?>
</body>
</html>