<?php
session_start();
require_once('config.php');
$connexion = getConnection();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérifiez si l'utilisateur est connecté
    if (!isset($_SESSION['email'])) {
        header('Location: connect.php');
        exit;
    }

    // Récupérez l'ID de l'annonce à ajouter
    $id_annonce = $_POST['id_annonce'];

    // Récupérez l'email de l'utilisateur à partir du formulaire
    $email_utilisateur = $_POST['email_utilisateur'];

    // Récupérez l'ID de l'utilisateur à partir de l'email

    $sql = "SELECT id FROM compte WHERE email = :email";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(array(':email' => $email_utilisateur));
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $id_utilisateur = $result['id'];

    // Générez la date de début et de fin
    $date_debut = date('Y-m-d');
    $date_fin = date('Y-m-d', strtotime('+14 days'));

    // Insérez les informations dans la table des emprunts
    $sql = "INSERT INTO emprunt (idbook, iduser, datedebut, datefin) VALUES (:idbook, :iduser, :datedebut, :datefin)";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(array(
        'idbook' => $id_annonce,
        'iduser' => $id_utilisateur,
        'datedebut' => $date_debut,
        'datefin' => $date_fin
    ));
    $sql = "UPDATE annonce SET stock = stock - 1 WHERE id = :id_annonce";
    $stmt = $connexion->prepare($sql);
    $stmt->execute(array(':id_annonce' => $id_annonce));
} ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmation</title>
    <style>
        /* Styles pour la fenêtre modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: #000000;
            color:white;
            /* Fond semi-transparent */
        }

        /* Contenu de la fenêtre modale */
        .modal-content {
            background-color: #000000;
            color:white;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        /* Styles pour le bouton de fermeture */
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
            <p>Votre livre a été ajouté à vos emprunts avec succès !</p>
        </div>
    </div>
    <script>
        var modal = document.getElementById("myModal");
        var span = document.getElementsByClassName("close")[0];

        // Quand l'utilisateur clique sur le bouton de fermeture, fermez la boîte de dialogue et redirigez
        span.onclick = function() {
            modal.style.display = "none";
            window.location.href = 'profil.php';
        }
        window.onload = function() {
            modal.style.display = "block";
        }
    </script>
</body>

</html>