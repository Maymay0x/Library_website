<?php
session_start();
ob_start(); // Démarre le tampon de sortie

if (isset($_SESSION['email'])) {
    // Inclure le fichier connexion.php pour utiliser la fonction getConnection()
    require_once('config.php');
    $pdo = getConnection();

    // Récupérer les informations actuelles du compte
    $email = htmlspecialchars($_SESSION['email']);
    $stmt = $pdo->prepare("SELECT * FROM compte WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user['rolec'] == 'waiting') {
        $_SESSION['decide'] = "wait";
        header('Location: error.php');
        exit;
    } elseif ($user['rolec'] === 'reject') {
        $_SESSION['decide'] = "reject";
        header('Location: error.php');
    } elseif ($user['rolec'] === 'accept') {
        $sql_validate_account = "UPDATE compte SET rolec = 'user' WHERE email = :email";
        $stmt_validate_account = $pdo->prepare($sql_validate_account);
        $stmt_validate_account->bindParam(':email', $_SESSION['email']);
        $stmt_validate_account->execute();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['lead'])) {
            header('Location: affichage.php');
            exit;
        }
        if (isset($_POST['del'])) {
            // Suppression du compte
            $sql = "SELECT COUNT(*) FROM emprunt WHERE iduser IN (SELECT id FROM compte WHERE email = :email)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['email' => $email]);
            $isloaning = $stmt->fetchColumn();

            if ($isloaning > 0) {
                $message = "Vous avez des emprunts en cours, vous ne pouvez donc pas supprimer votre compte.";
            } else {
                $sql = "DELETE FROM compte WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['email' => $email]);
                session_destroy();
                header('Location: connect.php');
                exit;
            }
        }


        if (isset($_POST['upd'])) {
            // Modification du compte
            $nom = !empty($_POST['nom']) ? htmlspecialchars($_POST['nom']) : $user['nom'];
            $prenom = !empty($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : $user['prenom'];
            $birth = !empty($_POST['birth']) ? htmlspecialchars($_POST['birth']) : $user['birth'];
            $rolec = !empty($_POST['rolec']) ? htmlspecialchars($_POST['rolec']) : $user['rolec'];
            $mdp = !empty($_POST['psswd']) ? htmlspecialchars($_POST['psswd']) : '';

            if (!empty($mdp)) {
                $mdpHash = password_hash($mdp, PASSWORD_DEFAULT); // Hacher le mot de passe
                $sql = "UPDATE compte SET nom = :nom, prenom = :prenom, birth = :birth, rolec = :rolec, psswd = :psswd WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'birth' => $birth,
                    'rolec' => $rolec,
                    'psswd' => $mdpHash,
                    'email' => $email
                ]);
            } else {
                $sql = "UPDATE compte SET nom = :nom, prenom = :prenom, birth = :birth, rolec = :rolec WHERE email = :email";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'birth' => $birth,
                    'rolec' => $rolec,
                    'email' => $email
                ]);
            }

            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        if (isset($_POST['logout'])) {
            session_destroy();
            header('Location: connect.php');
            exit;
        }
        /* -----------------------DEMANDE DE TRAITEMENT DU RENDU---------------------- */
        if (isset($_POST['rendre'])) {
            $ret_wait = "ret_waiting";
            $emprunt_id = $_POST['idloan'];
            $sql = "UPDATE emprunt SET statut_loan = :statut WHERE idloan = :idloan";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(array(
                'statut' => $ret_wait,
                'idloan' => $emprunt_id
            ));
        }
        /* -----------------------DEMANDE DE TRAITEMENT PROLONGATION------------------*/
        if (isset($_POST['prolonger'])) {
            $emprunt_id = $_POST['idloan'];
            $stmt = $pdo->prepare("SELECT nb_prolongations FROM emprunt WHERE idloan = :idloan");
            $stmt->execute(['idloan' => $emprunt_id]);
            $prolongations = $stmt->fetch(PDO::FETCH_ASSOC)['nb_prolongations'];
            $statement = $stmt->fetch(PDO::FETCH_ASSOC)['statut_loan'];
            if ($prolongations == 0) {
                $sql = "UPDATE emprunt SET statut_loan = 'waiting' WHERE idloan = :idloan";
                $stmt = $pdo->prepare($sql);
                $stmt->execute(['idloan' => $emprunt_id]);
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            }
        }
    }
    ob_end_flush(); // Envoie le tampon de sortie au navigateur
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Modification du compte</title>
        <link rel="stylesheet" href="css/profil.css" />
        <link rel="stylesheet" href="css/footer.css" />
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
                background-color: rgba(0, 0, 0, 0.5);
                /* Fond semi-transparent */
            }

            /* Contenu de la fenêtre modale */
            .modal-content {
                background-color: #000000;
                margin: 15% auto;
                padding: 20px;
                border: 1px solid #888;
                width: 80%;
                text-align: center;
            }
        </style>
    </head>

    <body>
    <div class="content">

          <nav>
    <ul>
        <li><a href="main.php">Menu</a></li>
        <li><a href='panier.php'>Mon Panier</a></li>
        <?php if ($user['rolec'] === "admin") : ?>
            <li><a href="#" onclick="document.forms['leadForm'].submit();">Gestion</a></li>
        <?php endif; ?>
        <li><a href="#" onclick="document.forms['logoutForm'].submit();">Déconnexion</a></li>
    </ul>
    <div class="animation start-home"></div>
</nav>
        <div class="modal" id="myModal">
            <div class="modal-content">
                <p id="modalMessage"></p>
            </div>
        </div>

        <script>
            // Afficher la fenêtre modale avec le message spécifié
            function afficherMessageModal(message) {
                var modal = document.getElementById("myModal");
                var modalMessage = document.getElementById("modalMessage");
                modalMessage.innerHTML = message;
                modal.style.display = "block";

                // Fermer la fenêtre modale lorsque l'utilisateur clique en dehors de celle-ci
                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            }

            <?php
            if (isset($_POST['rendre'])) {
                echo 'afficherMessageModal("Votre demande à bien été envoyé et sera traitée d\'ici peu.");';
            }
            ?>
        </script>
        <?php if (isset($message)) : ?>
            <script>
                afficherMessageModal("<?php echo $message; ?>");
            </script>
        <?php endif; ?>
        <div class="container">

        <?php
        echo "<h2>Bienvenue, " . htmlspecialchars($_SESSION['email']) . "! Vous êtes connecté.</h2>";
        ?>
        <form id="infos" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <label for="nom">Nom :</label>
            <input type="text" name="nom" placeholder="<?php echo htmlspecialchars($user['nom']); ?>">
            <input type="hidden" name="nom_old" value="<?php echo htmlspecialchars($user['nom']); ?>"><br>

            <label for="prenom">Prénom :</label>
            <input type="text" name="prenom" placeholder="<?php echo htmlspecialchars($user['prenom']); ?>">
            <input type="hidden" name="prenom_old" value="<?php echo htmlspecialchars($user['prenom']); ?>"><br>

            <label for="birth">Date de naissance :</label>
            <input type="date" name="birth" value="<?php echo htmlspecialchars($user['birth']); ?>">
            <input type="hidden" name="birth_old" value="<?php echo htmlspecialchars($user['birth']); ?>"><br>
            <?php if ($user['rolec'] === 'admin'){?>
            <label for="rolec">Role :</label>
            <select name="rolec" id="rolec">
                <option value="user" <?php if ($user['rolec'] == 'user') echo 'selected'; ?>>User</option>
                <option value="admin" <?php if ($user['rolec'] == 'admin') echo 'selected'; ?>>Admin</option>
            </select>
            <?php } ?>
            <p>Vous êtes actuellement <?php echo htmlspecialchars($user['rolec']); ?></p>
            <input type="hidden" name="rolec_old" value="<?php echo htmlspecialchars($user['rolec']); ?>"><br>

            <label for="psswd">Mot de passe :</label>
            <input type="password" name="psswd" placeholder="*************">
            <input type="hidden" name="psswd_old" value="<?php echo htmlspecialchars($user['psswd']); ?>"><br>
            <button type="submit" name="upd">Modifier son compte</button>
            <button type='submit' name='del'>Supprimer son compte</button>
        </form>
        </div>
        <form id="leadForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="lead">
        </form>
        <form id="delForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="del">
        </form>
        <form id="logoutForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
            <input type="hidden" name="logout">
        </form>

        <br>
        <?php
        // Récupérer les emprunts de l'utilisateur connecté
        $stmt = $pdo->prepare("SELECT * FROM emprunt JOIN annonce ON emprunt.idbook = annonce.id WHERE iduser = :iduser AND statut_loan != 'panier'");
        $stmt->execute(['iduser' => $user['id']]);
        $emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Afficher les emprunts de l'utilisateur dans un tableau
        echo "<h2>Emprunts de {$user['prenom']} {$user['nom']}</h2>";
        echo "<table>";
        echo "<tr><th>Titre</th><th>Auteur</th><th>Date de début</th><th>Date de fin</th><th colspan='2'>Prolonger / Rendre</th></tr>";
        foreach ($emprunts as $emprunt) {
            echo "<tr>";
            echo "<td>{$emprunt['titre']}</td>";
            echo "<td>{$emprunt['auteur']}</td>";
            echo "<td>{$emprunt['datedebut']}</td>";
            echo "<td>{$emprunt['datefin']}</td>";

            // Zone de traitement des prolongations
            if ($emprunt['nb_prolongations'] === -1) {
                echo "<td>Prolongation Refusé</td>";
            } else if ($emprunt['nb_prolongations'] >= 1) {
                echo "<td>Limite Atteinte</td>";
            } else if ($emprunt['statut_loan'] === "waiting") {
                echo "<td>En attente de confirmation</td>";
            } else if ($emprunt['statut_loan'] === 'Emprunt') {
                echo "<td><form action='{$_SERVER['PHP_SELF']}' method='post'><input type='hidden' name='idloan' value='{$emprunt['idloan']}'><button type='submit' name='prolonger'>Prolonger de 7 jours</button></form></td>";
            } else {
                echo "<td>Indisponible pour le moment</td>";
            }

            // Zone de traitement du rendu de livre
            if ($emprunt['statut_loan'] === "ret_waiting") {
                echo "<td>En cours de traitement</td>";
            } else {
                echo "<td><form action='{$_SERVER['PHP_SELF']}' method='post'><input type='hidden' name='idloan' value='{$emprunt['idloan']}'><button type='submit' name='rendre'>Rendre le livre</button></form>";
            }
            echo "</tr>";
        }
        echo "</table>";
        ?>
       </div> 
            <?php include 'footer.php'; ?>

    </body>

    </html>
<?php
} else {
    header('Location: connect.php');
    exit;
}
?>