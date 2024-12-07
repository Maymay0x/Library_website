<?php
session_start();
require_once('config.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Vérification que tous les champs sont remplis
    if (!empty($_POST['prenom']) && !empty($_POST['nom']) && !empty($_POST['birth']) && !empty($_POST['email']) && !empty($_POST['mdpuser'])) {
        $prenom = htmlspecialchars($_POST["prenom"]);
        $birth = htmlspecialchars($_POST["birth"]);
        $nom = htmlspecialchars($_POST["nom"]);
        $email = htmlspecialchars($_POST["email"]);
        $mdp = $_POST["mdpuser"]; // Ne pas filtrer le mot de passe, il sera haché

        // Hachage du mot de passe
        $mdpHash = password_hash($mdp, PASSWORD_DEFAULT);

        // Obtention de la connexion PDO en appelant la fonction
        $connexion = getConnection();

        // Vérifier si l'email est déjà utilisé
        $sql_check_email = "SELECT COUNT(*) AS email_count FROM compte WHERE email = :email";
        $stmt_check_email = $connexion->prepare($sql_check_email);
        $stmt_check_email->bindParam(':email', $email);
        $stmt_check_email->execute();
        $email_result = $stmt_check_email->fetch(PDO::FETCH_ASSOC);
        $email_count = $email_result['email_count'];

        // Si l'email est déjà utilisé, afficher un message d'erreur
        if ($email_count > 0) {
            // Affichage d'un modal d'erreur
            ?>
            <!DOCTYPE html>
            <html lang="fr">

            <head>
                <meta charset="UTF-8">
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Erreur</title>
                <style>
                    /* Styles pour le modal */
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
                        /* Fond semi-transparent */
                    }

                    /* Contenu du modal */
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
                        <p>L'adresse email est déjà associée à un compte existant.</p>
                    </div>
                </div>
                <script>
                    var modal = document.getElementById("myModal");
                    var span = document.getElementsByClassName("close")[0];

                    span.onclick = function() {
                        modal.style.display = "none";
                        window.location.href = 'connect.php';
                    }
                </script>
            </body>

            </html>
            <?php
            exit();
        }

        // Vérification du nombre de comptes existants
        $sql_count = "SELECT COUNT(*) AS total FROM compte";
        $stmt_count = $connexion->prepare($sql_count);
        $stmt_count->execute();
        $count_result = $stmt_count->fetch(PDO::FETCH_ASSOC);
        $total_comptes = $count_result['total'];

        // Détermination du rôle
        $role = $total_comptes == 0 ? 'admin' : 'waiting';

        // Requête SQL préparée pour l'insertion d'un nouvel utilisateur
        $sql = "INSERT INTO compte (nom, prenom, birth, email, psswd, rolec) VALUES (:nom, :prenom, :birth, :email, :mdpuser, :rolec)";

        $reqprep = $connexion->prepare($sql);

        // Liaison des paramètres
        $reqprep->bindParam(":nom", $nom);
        $reqprep->bindParam(':prenom', $prenom);
        $reqprep->bindParam(':birth', $birth);
        $reqprep->bindParam(':email', $email);
        $reqprep->bindParam(':mdpuser', $mdpHash);
        $reqprep->bindParam(':rolec', $role);

        // Exécution de la requête
        $reqprep->execute();

        // Si le rôle est admin, rediriger vers le profil
        if ($role === 'admin') {
            header("Location: profil.php");
            exit();
        }

        // Affichage de la confirmation
        ?>
        <!DOCTYPE html>
        <html lang="fr">

        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Confirmation</title>
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
                    background-color: #000000;
                    color:white;
                }

                .modal-content {
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
                    <p>Votre inscription va être traitée d'ici 24 heures !</p>
                </div>
            </div>
            <script>
                var modal = document.getElementById("myModal");
                var span = document.getElementsByClassName("close")[0];

                span.onclick = function() {
                    modal.style.display = "none";
                    window.location.href = 'connect.php';
                }
            </script>

        </body>

        </html>
        <?php
    } else {
        $erreur = "Veuillez remplir tous les champs du formulaire.";
        header("Location: connect.php");
        exit();
    }
}
?>