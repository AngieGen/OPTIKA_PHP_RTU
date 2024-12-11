<?php
session_start();
?>

<header class="header">
    <!-- Верхнее меню -->
    <div class="header-top">
        <!-- Логотип -->
        <a href="index.php" class="logo">
            <img src="images/logo_horizontal.png" alt="Acu Draugs">
        </a>

        <!-- Форма поиска -->
        <form class="search-form" method="GET" action="products.php">
            <input type="search" name="search" placeholder="Meklēt produktus..." aria-label="Search" required>
            <button type="submit">Meklēt</button>
        </form>

        <!-- Ссылки для пользователей -->
        <div class="user-links">
            <?php if (isset($_SESSION['role'])): ?>
                <?php if ($_SESSION['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php" class="admin-link">Administrācija</a>
                <?php else: ?>
                    <a href="account.php" class="account-link">Mans Profils</a>
                <?php endif; ?>
                <a href="logout.php" class="logout-link">Iziet</a>
            <?php else: ?>
                <a href="login.php" class="login-link">Ieiet/Reģistrēties</a>
            <?php endif; ?>
        </div>

        <!-- Ссылка на корзину (только для пользователей, не администраторов) -->
        <?php if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin'): ?>
            <a href="cart.php" class="cart-link">Grozs</a>
        <?php endif; ?>
    </div>

    <!-- Нижнее меню -->
    <nav class="header-bottom">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="#">Brilles</a>
                <ul class="dropdown">
                    <li><a href="products.php?category_id=1">Visas brilles</a></li>
                    <li><a href="products.php?subcategory_id=3">Vīriešiem</a></li>
                    <li><a href="products.php?subcategory_id=1">Sievietēm</a></li>
                    <li><a href="products.php?subcategory_id=5">Bērniem</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#">Saulesbrilles</a>
                <ul class="dropdown">
                    <li><a href="products.php?category_id=2">Visas saulesbrilles</a></li>
                    <li><a href="products.php?subcategory_id=4">Vīriešiem</a></li>
                    <li><a href="products.php?subcategory_id=2">Sievietēm</a></li>
                    <li><a href="products.php?subcategory_id=6">Bērniem</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#">Kontaktlēcas</a>
                <ul class="dropdown">
                    <li><a href="products.php?subcategory_id=7">Vienas dienas</a></li>
                    <li><a href="products.php?subcategory_id=8">Mēneša</a></li>
                    <li><a href="products.php?subcategory_id=9">Krāsainās</a></li>
                </ul>
            </li>
            <li class="nav-item">
                <a href="#">Aksesuāri</a>
                <ul class="dropdown">
                    <li><a href="products.php?subcategory_id=10">Futlāri</a></li>
                    <li><a href="products.php?subcategory_id=11">Acu pilieni</a></li>
                    <li><a href="products.php?subcategory_id=12">Brilles kopšana</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</header>

<?php
if (isset($_SESSION['cart_message'])) {
    // Устанавливаем класс CSS в зависимости от типа сообщения
    $alertClass = isset($_SESSION['cart_message_type']) && $_SESSION['cart_message_type'] === 'error' ? 'alert-danger' : 'alert-success';

    // Отображаем сообщение
    echo '<div class="alert ' . $alertClass . ' text-center">' . htmlspecialchars($_SESSION['cart_message']) . '</div>';

    // Удаляем сообщение и тип после отображения
    unset($_SESSION['cart_message']);
    unset($_SESSION['cart_message_type']);
}
?>