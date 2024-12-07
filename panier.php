<?php
session_start();
require_once('config.php');
$connexion = getConnection();

// Vérification de l'authentification de l'utilisateur
if (!isset($_SESSION['email'])) {
    header("Location: connect.php");
    exit();
}

// Vérification des soumissions de formulaires et exécution des actions appropriées
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Supprimer tous les éléments de la base de données
    if (isset($_POST['delete_all'])) {
        $sql_delete_all = "DELETE FROM emprunt WHERE iduser = (SELECT id FROM compte WHERE email = :email) AND statut_loan = 'panier'";
        $stmt_delete_all = $connexion->prepare($sql_delete_all);
        $stmt_delete_all->bindParam(':email', $_SESSION['email']);
        $stmt_delete_all->execute();
        // Rediriger vers cette même page après la suppression
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Valider tous les éléments de la base de données
    if (isset($_POST['validate_all'])) {
          // Sélectionner les idbook des emprunts à valider
          $sql_select_books = "SELECT idbook FROM emprunt WHERE iduser = (SELECT id FROM compte WHERE email = :email) AND statut_loan = 'panier'";
          $stmt_select_books = $connexion->prepare($sql_select_books);
          $stmt_select_books->bindParam(':email', $_SESSION['email']);
          $stmt_select_books->execute();
          $books = $stmt_select_books->fetchAll(PDO::FETCH_ASSOC);
  
          // Vérifier le stock pour chaque livre
          $insufficient_stock = false;
          foreach ($books as $book) {
              $sql_check_stock = "SELECT stock FROM annonce WHERE id = :idbook";
              $stmt_check_stock = $connexion->prepare($sql_check_stock);
              $stmt_check_stock->bindParam(':idbook', $book['idbook']);
              $stmt_check_stock->execute();
              $stock = $stmt_check_stock->fetch(PDO::FETCH_ASSOC)['stock'];
  
              if ($stock < 1) {
                  $insufficient_stock = true;
                  break;
              }
          }
  
          if ($insufficient_stock) {
              // Afficher un message d'erreur
              ?>
              <!DOCTYPE html>
              <html lang="fr">
              <head>
                  <meta charset="UTF-8">
                  <meta name="viewport" content="width=device-width, initial-scale=1.0">
                  <title>Erreur de Stock</title>
                  <style>
                      .modal {
                          display: block;
                          position: fixed;
                          z-index: 1;
                          left: 0;
                          top: 0;
                          width: 100%;
                          height: 100%;
                          overflow: auto;
                          background-color: rgba(0, 0, 0, 0.5);
                      }
                      .modal-content {
                          background-color: #fefefe;
                          margin: 15% auto;
                          padding: 20px;
                          border: 1px solid #888;
                          width: 80%;
                          text-align: center;
                          position: relative;
                      }
                      .close {
                          color: #aaa;
                          float: right;
                          font-size: 28px;
                          font-weight: bold;
                      }
                      .close:hover,
                      .close:focus {
                          color: black;
                          text-decoration: none;
                          cursor: pointer;
                      }
                  </style>
              </head>
              <body>
                  <div id="myModal" class="modal">
                      <div class="modal-content">
                          <span class="close">&times;</span>
                          <p>Stock insuffisant pour valider tous les emprunts.</p>
                      </div>
                  </div>
                  <script>
                      var modal = document.getElementById("myModal");
                      var span = document.getElementsByClassName("close")[0];
  
                      span.onclick = function() {
                          modal.style.display = "none";
                          window.location.href = '<?= $_SERVER["PHP_SELF"] ?>';
                      }
                  </script>
              </body>
              </html>
              <?php
              exit();
          }
        // Mettre à jour le statut des emprunts
        $sql_validate_all = "UPDATE emprunt SET statut_loan = 'Emprunt' WHERE iduser = (SELECT id FROM compte WHERE email = :email) AND statut_loan = 'panier'";
        $stmt_validate_all = $connexion->prepare($sql_validate_all);
        $stmt_validate_all->bindParam(':email', $_SESSION['email']);
        $stmt_validate_all->execute();

        // Décrémenter le stock de chaque livre emprunté
        $sql_update_stock = "UPDATE annonce SET stock = stock - 1 WHERE id = :idbook";
        $stmt_update_stock = $connexion->prepare($sql_update_stock);
        foreach ($books as $book) {
            $stmt_update_stock->bindParam(':idbook', $book['idbook']);
            $stmt_update_stock->execute();
        }

        // Rediriger vers cette même page après la validation
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Supprimer un élément de la base de données
    if (isset($_POST['delete_item'])) {
        $idloan = $_POST['delete_item'];
        $sql_delete_item = "DELETE FROM emprunt WHERE idloan = :idloan AND iduser = (SELECT id FROM compte WHERE email = :email)";
        $stmt_delete_item = $connexion->prepare($sql_delete_item);
        $stmt_delete_item->bindParam(':idloan', $idloan);
        $stmt_delete_item->bindParam(':email', $_SESSION['email']);
        $stmt_delete_item->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // Valider un élément de la base de données
    if (isset($_POST['validate_item'])) {
        $idloan = $_POST['validate_item'];

        // Récupérer l'idbook de l'emprunt à valider
        $sql_select_book = "SELECT idbook FROM emprunt WHERE idloan = :idloan AND iduser = (SELECT id FROM compte WHERE email = :email)";
        $stmt_select_book = $connexion->prepare($sql_select_book);
        $stmt_select_book->bindParam(':idloan', $idloan);
        $stmt_select_book->bindParam(':email', $_SESSION['email']);
        $stmt_select_book->execute();
        $book = $stmt_select_book->fetch(PDO::FETCH_ASSOC);

        // Vérifier le stock du livre
        $sql_check_stock = "SELECT stock FROM annonce WHERE id = :idbook";
        $stmt_check_stock = $connexion->prepare($sql_check_stock);
        $stmt_check_stock->bindParam(':idbook', $book['idbook']);
        $stmt_check_stock->execute();
        $stock = $stmt_check_stock->fetch(PDO::FETCH_ASSOC)['stock'];

        if ($stock < 1) {
            // Afficher un message d'erreur
            ?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Erreur de Stock</title>
                <style>
                    .modal {
                        display: block;
                        position: fixed;
                        z-index: 1;
                        left: 0;
                        top: 0;
                        width: 100%;
                        height: 100%;
                        overflow: auto;
                        background-color: rgba(0, 0, 0, 0.5);
                    }
                    .modal-content {
                        background-color: #fefefe;
                        margin: 15% auto;
                        padding: 20px;
                        border: 1px solid #888;
                        width: 80%;
                        text-align: center;
                        position: relative;
                    }
                    .close {
                        color: #aaa;
                        float: right;
                        font-size: 28px;
                        font-weight: bold;
                    }
                    .close:hover,
                    .close:focus {
                        color: black;
                        text-decoration: none;
                        cursor: pointer;
                    }
                </style>
            </head>
            <body>
                <div id="myModal" class="modal">
                    <div class="modal-content">
                        <span class="close">&times;</span>
                        <p>Livre en rupture de stock.</p>
                    </div>
                </div>
                <script>
                    var modal = document.getElementById("myModal");
                    var span = document.getElementsByClassName("close")[0];

                    span.onclick = function() {
                        modal.style.display = "none";
                        window.location.href = '<?= $_SERVER["PHP_SELF"] ?>';
                    }
                </script>
            </body>
            </html>
            <?php
            exit();
        }


        // Mettre à jour le statut de l'emprunt
        $sql_validate_item = "UPDATE emprunt SET statut_loan = 'Emprunt' WHERE idloan = :idloan AND iduser = (SELECT id FROM compte WHERE email = :email)";
        $stmt_validate_item = $connexion->prepare($sql_validate_item);
        $stmt_validate_item->bindParam(':idloan', $idloan);
        $stmt_validate_item->bindParam(':email', $_SESSION['email']);
        $stmt_validate_item->execute();

        // Décrémenter le stock du livre emprunté
        $sql_update_stock = "UPDATE annonce SET stock = stock - 1 WHERE id = :idbook";
        $stmt_update_stock = $connexion->prepare($sql_update_stock);
        $stmt_update_stock->bindParam(':idbook', $book['idbook']);
        $stmt_update_stock->execute();

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}

// Récupération de l'email de l'utilisateur connecté
$email = $_SESSION['email'];

// Sélection des emprunts de l'utilisateur connecté avec un statut_loan de 'panier' et les détails du livre
$sql_emprunts = "SELECT e.idloan, l.titre, l.picture, l.auteur, l.editeur FROM emprunt e INNER JOIN annonce l ON e.idbook = l.id WHERE e.iduser = (SELECT id FROM compte WHERE email = :email) AND e.statut_loan = 'panier'";
$stmt_emprunts = $connexion->prepare($sql_emprunts);
$stmt_emprunts->bindParam(':email', $email);
$stmt_emprunts->execute();
$emprunts = $stmt_emprunts->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/panier.css" />
    <title>Liste des emprunts</title>
    
</head>

<body>
    <nav>
        <button onclick="window.location.href='main.php'">Menu</button>
        <button onclick="window.location.href='profil.php'">Profil</button>
        <div class="animation start-home"></div>
    </nav>
    <h1>Mon panier</h1>
    <form method="POST">
        <button type="submit" class="delete-all" name="delete_all">Supprimer tous les éléments</button>
        <button type="submit" class="validate-all" name="validate_all">Valider tous les éléments</button>
    </form>
    <table>
        <tr>
            <th>ID Emprunt</th>
            <th>Image</th>
            <th>Titre du Livre</th>
            <th>Auteur</th>
            <th>Éditeur</th>
            <th colspan="2">Actions</th>
        </tr>
        <?php foreach ($emprunts as $emprunt) : ?>
            <tr>
                <td><?= $emprunt['idloan']; ?></td>
                <td><img src="<?= $emprunt['picture']; ?>" alt="Image du livre"></td>
                <td><?= $emprunt['titre']; ?></td>
                <td><?= $emprunt['auteur']; ?></td>
                <td><?= $emprunt['editeur']; ?></td>
                <td>
                    <form method="POST">
                        <button type="submit" name="delete_item" value="<?= $emprunt['idloan']; ?>">Supprimer</button>
                    </form>
                </td>
                <td>
                    <form method="POST">
                        <button type="submit" name="validate_item" value="<?= $emprunt['idloan']; ?>">Valider</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>

</html>
