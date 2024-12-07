<?php session_start(); 
require_once('config.php');
            $connexion = getConnection();?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel='stylesheet' href='css/rech.css' />
    <title>Recherche d'annonces</title>

</head>

<body>
    <nav>
        <a href="main.php">Menu</a>
        <a href='panier.php'>Mon Panier</a>
        <?php

        if (isset($_SESSION['email'])) {
            echo ("<a href='profil.php'>Mon Profil</a>");
        } else {
            echo ("<a href='connect.php'>Se connecter</a>");
        } ?>
        <div class="animation start-home"></div>
    </nav>
    <form id="barrerech" action="recherche.php" method="GET">
        <label for="titre">Titre:</label>
        <input type="text" id="titre" name="titre">

        <label for="auteur">Auteur:</label>
        <input type="text" id="auteur" name="auteur">

        <label for="genre">Genre:</label>
        <select id="genre" name="genre">
            <option value="">Sélectionnez un genre</option>
            <?php
            // Connexion à la base de données

            // Récupération des genres disponibles
            $sql_genres = "SELECT DISTINCT genre FROM annonce";
            $stmt_genres = $connexion->query($sql_genres);
            $genres = $stmt_genres->fetchAll(PDO::FETCH_COLUMN);

            // Affichage des options
            foreach ($genres as $genre) {
                echo "<option class='custom-op' value='$genre'>$genre</option>";
            }
            ?>
        </select>

        <label for="tri">Tri:</label>
        <select id="tri" name="tri">
            <option value="titre_asc">Titre croissant</option>
            <option value="titre_desc">Titre décroissant</option>
            <option value="auteur_asc">Auteur croissant</option>
            <option value="auteur_desc">Auteur décroissant</option>
            <option value="genre_asc">Genre croissant</option>
            <option value="genre_desc">Genre décroissant</option>
        </select>

        <button type="submit">Rechercher</button>
    </form>

    <?php
    // Traitement de la recherche
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        // Conditions de recherche
        $conditions = [];
        $params = [];

        // Ajout des critères de recherche si les champs sont renseignés
        if (isset($_GET['titre']) && !empty($_GET['titre'])) {
            $conditions[] = "titre LIKE :titre";
            $params[':titre'] = '%' . $_GET['titre'] . '%';
        }
        if (isset($_GET['auteur']) && !empty($_GET['auteur'])) {
            $conditions[] = "auteur LIKE :auteur";
            $params[':auteur'] = '%' . $_GET['auteur'] . '%';
        }
        if (isset($_GET['genre']) && !empty($_GET['genre'])) {
            $conditions[] = "genre LIKE :genre";
            $params[':genre'] = '%' . $_GET['genre'] . '%';
        }

        // Construction de la requête SQL
        $sql = "SELECT * FROM annonce";
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }

        // Ajout de l'option de tri si elle est définie dans $_GET
        if (isset($_GET['tri'])) {
            switch ($_GET['tri']) {
                case 'titre_asc':
                    $sql .= " ORDER BY titre ASC";
                    break;
                case 'titre_desc':
                    $sql .= " ORDER BY titre DESC";
                    break;
                case 'auteur_asc':
                    $sql .= " ORDER BY auteur ASC";
                    break;
                case 'auteur_desc':
                    $sql .= " ORDER BY auteur DESC";
                    break;
                case 'genre_asc':
                    $sql .= " ORDER BY genre ASC";
                    break;
                case 'genre_desc':
                    $sql .= " ORDER BY genre DESC";
                    break;
            }
        }

        // Exécution de la requête SQL
        $stmt = $connexion->prepare($sql);
        $stmt->execute($params);
        $annonces = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Récupérer les emprunts et paniers de l'utilisateur
        $userEmprunts = [];
        $userPanier = [];
        if (isset($_SESSION['email'])) {
            $userEmail = $_SESSION['email'];
            $sql_user_emprunts = "SELECT idbook FROM emprunt WHERE iduser = (SELECT id FROM compte WHERE email = :email) AND (statut_loan = 'Emprunt')";
            $stmt_user_emprunts = $connexion->prepare($sql_user_emprunts);
            $stmt_user_emprunts->bindParam(':email', $userEmail);
            $stmt_user_emprunts->execute();
            $userEmprunts = $stmt_user_emprunts->fetchAll(PDO::FETCH_COLUMN);

            $sql_user_panier = "SELECT idbook FROM emprunt WHERE iduser = (SELECT id FROM compte WHERE email = :email) AND statut_loan = 'panier'";
            $stmt_user_panier = $connexion->prepare($sql_user_panier);
            $stmt_user_panier->bindParam(':email', $userEmail);
            $stmt_user_panier->execute();
            $userPanier = $stmt_user_panier->fetchAll(PDO::FETCH_COLUMN);
        }
        // Affichage des résultats
        if ($annonces) {
            foreach ($annonces as $annonce) {
                echo "<div class='annonce'>";
                echo "<img src='{$annonce['picture']}' alt='{$annonce['titre']}'>";
                echo "<div class='annonce-details'>";
                echo "<h3>{$annonce['titre']}</h3>";
                echo "<div class='annonce-p'>";
                echo "<p>Auteur: {$annonce['auteur']}</p>";
                echo "<p>Genre: {$annonce['genre']}</p>";
                echo "<p>Stock: {$annonce['stock']}</p>";

                $alreadyInEmprunts = in_array($annonce['id'], $userEmprunts);
                $alreadyInPanier = in_array($annonce['id'], $userPanier);

                if (isset($_SESSION['email'])) {
                    if (!$alreadyInEmprunts && !$alreadyInPanier && $annonce['stock'] > 0) {
                        echo "<form action='ajout_emprunt.php' method='POST'>";
                        echo "<input type='hidden' name='id_annonce' value='{$annonce['id']}' />";
                        echo "<input type='hidden' name='email_utilisateur' value='{$_SESSION['email']}' />";
                        echo "<button type='submit'>Ajouter à mes emprunts</button>";
                        echo "</form>";

                        echo "<form action='ajout_panier.php' method='POST'>";
                        echo "<input type='hidden' name='id_annonce' value='{$annonce['id']}' />";
                        echo "<input type='hidden' name='email_utilisateur' value='{$_SESSION['email']}' />";
                        echo "<button type='submit'>Ajouter au panier</button>";
                        echo "</form>";
                    } else {
                        if ($alreadyInEmprunts) {
                            echo "<p>Vous avez déjà emprunté ce livre.</p>";
                        } else if ($alreadyInPanier) {
                            echo "<p>Ce livre est déjà dans votre panier.</p>";
                        } else if ($annonce['stock'] <= 0) {
                            echo "<p>En rupture de stock.</p>";
                        }
                    }
                } else {
                    echo "<form action='connect.php' method='GET'>";
                    echo "<button type='submit'>Ajouter à mes emprunts</button>";
                    echo "</form>";

                    echo "<form action='connect.php' method='GET'>";
                    echo "<button type='submit'>Ajouter au panier</button>";
                    echo "</form>";
                }

                echo "</div>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "<p>Aucun résultat trouvé.</p>";
        }
    }
    ?>

</body>

</html>