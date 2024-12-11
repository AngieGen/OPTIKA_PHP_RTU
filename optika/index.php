<!DOCTYPE html>
<html lang="lv">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Acu Draugs - Tava Optika</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
   

  </head>
  <body>
    <!-- Верхнее меню -->
    <?php require_once "header.php"; ?>
<div class="page-container">


    <!-- Баннер -->
<section class="hero">
  <div class="carousel">
    <div class="carousel-inner">
      <!-- Слайды -->
      <div class="carousel-item active">
        <img src="images/snoopdog.jpg" alt="Slide 1">
        <div class="carousel-caption">
          <h2>Laipni lūdzam Acu Draugs!</h2>
          <p>Labākā vieta, kur atrast savas jaunās brilles un lēcas.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="images/snoopsun.jpg" alt="Slide 2">
        <div class="carousel-caption">
          <h2>Plaša izvēle saulesbriļļu!</h2>
          <p>Stils un aizsardzība vienuviet.</p>
        </div>
      </div>
      <div class="carousel-item">
        <img src="images/snoop2.jpg" alt="Slide 3">
        <div class="carousel-caption">
          <h2>Piedāvājumi bērniem</h2>
          <p>Drošība un komforts jūsu mazajiem.</p>
        </div>
      </div>
    </div>

    <!-- Кнопки навигации -->
    <button class="carousel-control prev" onclick="prevSlide()">&#10094;</button>
    <button class="carousel-control next" onclick="nextSlide()">&#10095;</button>
  </div>
</section>



<?php
// Подключение к базе данных
include 'config.php';

// Функция для выборки продуктов по подкатегории
function getProductsBySubCategory($conn, $subcategoryId) {
    $query = $conn->prepare("SELECT * FROM myproducts WHERE subcategory_id = ? LIMIT 3");
    $query->bind_param("i", $subcategoryId);
    $query->execute();
    return $query->get_result();
}

// Получение продуктов для детей, мужчин и женщин
$childrenProducts = getProductsBySubCategory($conn, 5);
$menProducts = getProductsBySubCategory($conn, 3);
$womenProducts = getProductsBySubCategory($conn, 1);
?>

<?php if (isset($_SESSION['message'])): ?>
    <div class="alert alert-success text-center">
        <?php 
            echo $_SESSION['message'];
            unset($_SESSION['message']); // Удаляем сообщение после отображения
        ?>
    </div>
<?php endif; ?>

<section class="quiz-section py-5">
    <div class="container text-center">
        <h2>Noskaidrojiet, kādas brilles Jums piestāv!</h2>
        <p>Veiciet mūsu ātro stila testu un saņemiet personalizētus ieteikumus.</p>
        <a href="quiz.php" class="btn btn-primary btn-lg">Sākt stila testu</a>
    </div>
</section>

<section class="popular-products py-5">
    <div class="container">
        <h3 class="text-center mb-4">Populārie produkti</h3>

        <!-- Продукты для детей -->
        <h4>Bērnu brilles</h4>
        <div class="row">
            <?php while ($product = mysqli_fetch_assoc($childrenProducts)) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo $product['image']; ?>" class="card-img-top product-image" alt="<?php echo $product['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text"><?php echo $product['description']; ?></p>
                            <p class="card-text"><strong>€ <?php echo $product['price']; ?></strong></p>
                             <!-- Форма для добавления в корзину -->
                            <form method="POST" action="add_to_cart.php">
                              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                              <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                              <button type="submit" class="btn btn-warning">Pievienot grozam</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="text-center mt-3">
            <a href="products.php?subcategory_id=5" class="btn btn-primary">Vairāk</a> <!-- Детские очки -->
        </div>

        <!-- Продукты для мужчин -->
        <h4>Vīriešu brilles</h4>
        <div class="row">
            <?php while ($product = mysqli_fetch_assoc($menProducts)) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo $product['image']; ?>" class="card-img-top product-image" alt="<?php echo $product['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text"><?php echo $product['description']; ?></p>
                            <p class="card-text"><strong>€ <?php echo $product['price']; ?></strong></p>
                             <!-- Форма для добавления в корзину -->
                             <form method="POST" action="add_to_cart.php">
                              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                              <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                              <button type="submit" class="btn btn-warning">Pievienot grozam</button>
                             </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="text-center mt-3">
            <a href="products.php?subcategory_id=3" class="btn btn-primary">Vairāk</a> <!-- Мужские очки -->
        </div>

        <!-- Продукты для женщин -->
        <h4>Sieviešu brilles</h4>
        <div class="row">
            <?php while ($product = mysqli_fetch_assoc($womenProducts)) { ?>
                <div class="col-md-4">
                    <div class="card">
                        <img src="<?php echo $product['image']; ?>" class="card-img-top product-image" alt="<?php echo $product['name']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo $product['name']; ?></h5>
                            <p class="card-text"><?php echo $product['description']; ?></p>
                            <p class="card-text"><strong>€ <?php echo $product['price']; ?></strong></p>
                             <!-- Форма для добавления в корзину -->
                            <form method="POST" action="add_to_cart.php">
                              <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                              <input type="number" name="quantity" value="1" min="1" class="form-control mb-2">
                              <button type="submit" class="btn btn-warning">Pievienot grozam</button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="text-center mt-3">
            <a href="products.php?subcategory_id=1" class="btn btn-primary">Vairāk</a> <!-- Женские очки -->
        </div>
    </div>
</section>
</div>
    <!-- Футер -->
   <?php require_once "footer.php"; ?>
<script src="app.js"></script>
  </body>
</html>
