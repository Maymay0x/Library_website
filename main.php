<?php
session_start();
require_once('config.php');
$connexion = getConnection();
//requete pour les livres à la une
$sql = "SELECT id, picture, titre FROM annonce LIMIT 25";
$stmt = $connexion->prepare($sql);
$stmt->execute();

$annonces = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $annonces[] = $row;
}
//requete pour les livres du genre fantasy
$sql_f = "SELECT id, picture, titre FROM annonce WHERE genre ='fantasy' LIMIT 25";
$stmt_f = $connexion->prepare($sql_f);
$stmt_f->execute();

$annonces_f = [];
while ($row_f = $stmt_f->fetch(PDO::FETCH_ASSOC)) {
    $annonces_f[] = $row_f;
}
//requete pour les manga
$sql_m = "SELECT id, picture, titre FROM annonce WHERE genre LIKE '%manga%' LIMIT 25";
$stmt_m = $connexion->prepare($sql_m);
$stmt_m->execute();

$annonces_m = [];
while ($row_m = $stmt_m->fetch(PDO::FETCH_ASSOC)) {
    $annonces_m[] = $row_m;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/main.css" />
    <link rel="stylesheet" href="css/footer.css" />
    <script src="js/main.js"></script>

    <title>MyLib</title>
</head>

<body>
    <!-- Header -->
    <header>
        <div class="my-carousel-container">
            <div class="my-carousel">
                <div class="my-slide active">
                    <img src="./Source/hihi.jpg" alt="Slide 1">
                </div>
                <div class="my-slide">
                    <img src="./Source/acotar.png" alt="Slide 2">
                </div>
                <div class="my-slide">
                    <img src="./Source/math.jpg" alt="Slide 3">
                </div>
                <div class="my-slide">
                    <img src="./Source/hp.jpg" alt="Slide 3">
                </div>
                <div class="my-slide">
                    <img src="./Source/hk.jpg" alt="Slide 3">
                </div>
            </div>
            <button class="my-prev" onclick="prevSlide()">&lt;</button>
            <button class="my-next" onclick="nextSlide()">&gt;</button>
        </div>

    </header>

    <!-- Navigation -->
    <nav>
        <a href="recherche.php">Rechercher</a>
        <a href='panier.php'>Mon Panier</a>
        <?php

        if (isset($_SESSION['email'])) {
            echo ("<a href='profil.php'>Mon Profil</a>");
        } else {
            echo ("<a href='connect.php'>Se connecter</a>");
        } ?>
        <div class="animation start-home"></div>
    </nav>

    <!-- Section Nos livres à la une -->
    <h2>NOS LIVRES A LA UNE </h2>
    <div class="carousel-container">
        <button class="carousel-button prev" onclick="livreprevSlide()">&lt;</button>
        <div class="carousel" id="carousel">
            <?php foreach ($annonces as $annonce) : ?>
                <a href="recherche.php?titre=<?php echo $annonce['titre']; ?>" class="book">
                    <img src="<?php echo $annonce['picture']; ?>" alt="Livre">
                </a>
            <?php endforeach; ?>
        </div>
        <button class="carousel-button next" onclick="livrenextSlide()">&gt;</button>
    </div>

    <!-- Section Nos livres fantasy -->
    <h2>NOS LIVRES FANTASY ANGLAIS </h2>
    <div class="carousel-container">
        <button class="carousel-button prev" onclick="fantasyprevSlide()">&lt;</button>
        <div class="carousel" id="carouself">
            <?php foreach ($annonces_f as $annonce_f) : ?>
                <a href="recherche.php?titre=<?php echo $annonce_f['titre']; ?>" class="book">
                    <img src="<?php echo $annonce_f['picture']; ?>" alt="Livre">
                </a>
            <?php endforeach; ?>
        </div>
        <button class="carousel-button next" onclick="fantasynextSlide()">&gt;</button>
    </div>

    <!-- Section Nos Manga -->
    <h2>NOS MANGA </h2>
    <div class="carousel-container">
        <button class="carousel-button prev" onclick="mangaprevSlide()">&lt;</button>
        <div class="carousel" id="carouselm">
            <?php foreach ($annonces_m as $annonce_m) : ?>
                <a href="recherche.php?titre=<?php echo $annonce_m['titre']; ?>" class="book">
                    <img src="<?php echo $annonce_m['picture']; ?>" alt="Livre">
                </a>
            <?php endforeach; ?>
        </div>
        <button class="carousel-button next" onclick="manganextSlide()">&gt;</button>
    </div>
    <?php include 'footer.php'; ?>


            </body>
            </html>