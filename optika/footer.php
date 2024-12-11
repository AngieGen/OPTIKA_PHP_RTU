<footer class="footer">
    <div class="footer-box">
        <!-- Логотип -->
        <div class="footer-logo">
            <a href="index.php">
                <img src="images/acudraugs_mainlogo.png" alt="Acu Draugs Logo" />
            </a>
        </div>

        <!-- Верхнее меню в футере -->
        <div class="footer-menu">
            <ul>
                <li><a href="products.php?category_id=1">Brilles</a></li>
                <li><a href="products.php?category_id=2">Saulesbrilles</a></li>
                <li><a href="products.php?category_id=3">Kontaktlēcas</a></li>
                <li><a href="products.php?category_id=4">Aksesuāri</a></li>
            </ul>
        </div>

        <!-- Ссылки: корзина и авторизация -->
        <div class="footer-actions">
            <a href="cart.php" class="footer-cart">Grozs</a>
            <?php if (isset($_SESSION['role'])) { ?>
                <a href="logout.php" class="footer-auth">Iziet</a>
            <?php } else { ?>
                <a href="login.php" class="footer-auth">Ieiet/Reģistrēties</a>
            <?php } ?>
        </div>
            <hr>
        <!-- Основной текст -->
        <div class="footer-content">
            <p>&copy; 2024 Acu Draugs. Visas tiesības aizsargātas.</p>
            <p><a href="#" class="privacy-policy">Privātuma politika</a></p>
        </div>
    </div>
</footer>