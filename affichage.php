<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/aff.css">
    <link rel="stylesheet" href="css/footer.css" />
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var thumbnails = document.querySelectorAll('.thumbnail');
            thumbnails.forEach(function(thumbnail) {
                thumbnail.addEventListener('mouseover', function() {
                    var enlargedImage = new Image();
                    enlargedImage.src = this.src;
                    enlargedImage.classList.add('enlarged');
                    document.body.appendChild(enlargedImage);
                    centerEnlargedImage(enlargedImage);
                    thumbnail.addEventListener('mouseout', function() {
                        enlargedImage.remove();
                    });
                });
            });

            function centerEnlargedImage(image) {
                var windowWidth = window.innerWidth;
                var windowHeight = window.innerHeight;
                var imageWidth = image.width;
                var imageHeight = image.height;
                var left = (windowWidth - imageWidth) / 2;
                var top = (windowHeight - imageHeight) / 2;
                image.style.position = 'fixed'; // Positionne l'image par rapport à la fenêtre du navigateur
                image.style.left = left + 'px';
                image.style.top = top + 'px';
                image.style.zIndex = '9999'; // Assure que l'image est au-dessus de tous les autres éléments
            }
        });
    </script>
</head>

<body>
    <?php
    session_start();

    if (!isset($_SESSION['email'])) {
        header('Location: connect.php');
        exit;
    }
    require_once('config.php');
    $pdo = getConnection();
    // Traitement de la déconnexion
    if (isset($_POST['logout'])) {
        // Détruire la session et rediriger vers la page de connexion
        session_destroy();
        header('Location: connect.php');
        exit;
    }
    $email = $_SESSION['email'];
    // Vérifier si la section active est définie dans la variable de session, sinon la définir par défaut comme gestion des comptes
    if (!isset($_SESSION['active_section'])) {
        $_SESSION['active_section'] = 'comptes'; // Par défaut, définissez-le comme "gestion des comptes"
    }

    // Si la méthode de requête est POST et que la clé 'section' est définie dans $_POST, mettez à jour la section active
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['section'])) {
        $_SESSION['active_section'] = $_POST['section'];
    }


    // Logique pour gérer les comptes
    if ($_SESSION['active_section'] === 'comptes') {
        // Suppression du compte
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
            $id = $_POST['delete'];
            $sql = "DELETE FROM compte WHERE id = :id";
            $reqprep = $pdo->prepare($sql);
            $reqprep->execute(array('id' => $id));
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }
        // Mise à jour du compte
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
            $id = $_POST['id'];
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $birth = $_POST['birth'];
            $email = $_POST['email'];
            $rolec = $_POST['rolec'];
            $prepsswd = $_POST['psswd'];
            $psswd = password_hash($prepsswd, PASSWORD_DEFAULT);
            $sql = "UPDATE compte SET nom = :nom, prenom = :prenom, birth = :birth, email = :email, rolec = :rolec, psswd = :psswd WHERE id = :id";
            $reqprep = $pdo->prepare($sql);
            $reqprep->execute(
                array(
                    'nom' => $nom,
                    'prenom' => $prenom,
                    'birth' => $birth,
                    'email' => $email,
                    'rolec' => $rolec,
                    'psswd' => $psswd,
                    'id' => $id
                )
            );
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        // Ajout d'un nouveau compte
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add'])) {
            $new_nom = $_POST['new_nom'];
            $new_prenom = $_POST['new_prenom'];
            $new_birth = $_POST['new_birth'];
            $new_email = $_POST['new_email'];
            $new_rolec = $_POST['new_rolec'];
            $prepsswd = $_POST['new_psswd'];
            $new_psswd = password_hash($prepsswd, PASSWORD_DEFAULT);
            $sql = "SELECT * FROM compte WHERE email = :email";
            $reqprep = $pdo->prepare($sql);
            $res = $reqprep->execute(array('email' => $new_email));
            var_dump($res);
            if ($reqprep->rowCount() == 0) {
                $sql = "INSERT INTO compte (nom, prenom, birth, email, rolec, psswd) VALUES (:new_nom, :new_prenom, :new_birth, :new_email, :new_rolec, :new_psswd)";
                $reqprep = $pdo->prepare($sql);
                $reqprep->execute(
                    array(
                        'new_nom' => $new_nom,
                        'new_prenom' => $new_prenom,
                        'new_birth' => $new_birth,
                        'new_email' => $new_email,
                        'new_rolec' => $new_rolec,
                        'new_psswd' => $new_psswd
                    )
                );
                header('Location: ' . $_SERVER['PHP_SELF']);
                exit;
            } else {
                header('Location: error.php');
            }
        }


        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['accept'])) {
            $id = $_POST['id'];
            $sql = "UPDATE compte SET rolec = 'accept' WHERE id = :id";
            $reqprep = $pdo->prepare($sql);
            $reqprep->execute(array('id' => $id));
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['denied'])) {
            $id = $_POST['id'];
            $sql = "UPDATE compte SET rolec = 'reject' WHERE id = :id";
            $reqprep = $pdo->prepare($sql);
            $reqprep->execute(array('id' => $id));
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }


        // Sélection des comptes autres que le vôtre
        $sql = "SELECT * FROM compte WHERE email != :email AND email is NOT NULL ";
        $reqprep = $pdo->prepare($sql);
        $reqprep->execute(array('email' => $email));

        // Barre de navigation
        echo "<nav>";
        echo "<button onclick=\"window.location.href='main.php'\">Menu</button>";
        echo "<form method='post'>";
        echo "<button type='submit' name='section' value='annonces'>Gérer les annonces</button>";
        echo "<button type='submit' name='section' value='emprunts'>Gérer les emprunts</button>";
        echo "</form>";
        // Formulaire de déconnexion
        echo "<form id=dec action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
        echo "<button type='submit' name='logout'>Déconnexion</button>";
        echo "</form>";
        echo "<div class='animation start-home'></div>";
        echo "</nav>";
        // Formulaire d'ajout de compte
        echo "<div class=form_compte>";
        echo "<h2 class=ajout>Ajouter un compte</h2>";
        echo "<form class='infos' method='post'>";
        echo "Nom :<br>";
        echo "<input type='text' name='new_nom' required><br>";
        echo "Prénom : <br>";
        echo "<input type='text' name='new_prenom' required><br>";
        echo "Date de naissance :<br>";
        echo "<input type='date' name='new_birth' max='" . date('Y-m-d') . "' required> <br>";
        echo "Email :<br>";
        echo "<input type='email' name='new_email' required><br>";
        echo "Rôle :<br>";
        echo "<select name='new_rolec'>";
        echo "<option value='admin'>Admin</option>";
        echo "<option value='user'>User</option>";
        echo "</select><br>";
        echo "Mot de passe:<br>";
        echo "<input type='password' name='new_psswd' required><br>";
        echo "<button type='submit' name='add'>Ajouter</button>";
        echo "</form>";
        echo "</div>";

        // Affichage des comptes
        echo "<h1 id='comptes'>Gérer les comptes</h1>";
        echo "<table class='table_input'>";
        echo "<tr><th>ID</th><th>Nom</th><th>Prénom</th><th>Date de naissance</th><th>Email</th><th>Rôle</th><th>Mot de passe</th><th colspan='2'>Action</th></tr>";

        while ($prsn = $reqprep->fetch(PDO::FETCH_ASSOC)) {
            // Vérifier si l'utilisateur a des emprunts en cours
            $sql_check_loans = "SELECT COUNT(*) AS loan_count FROM emprunt WHERE iduser = :iduser";
            $stmt_check_loans = $pdo->prepare($sql_check_loans);
            $stmt_check_loans->execute(['iduser' => $prsn['id']]);
            $loan_count = $stmt_check_loans->fetch(PDO::FETCH_ASSOC)['loan_count'];
        
            echo "<tr>";
            echo "<form method='post'>"; // Formulaire de mise à jour individuel
            echo "<td>" . htmlspecialchars($prsn['id']) . "</td>"; // Échappe les données affichées
            echo "<td><input type='text' name='nom' value='" . htmlspecialchars($prsn['nom']) . "'></td>";
            echo "<td><input type='text' name='prenom' value='" . htmlspecialchars($prsn['prenom']) . "'></td>";
            echo "<td><input type='date' name='birth' value='" . htmlspecialchars($prsn['birth']) . "' max='" . date('Y-m-d') . "'></td>";
            echo "<td><input type='email' name='email' value='" . htmlspecialchars($prsn['email']) . "'></td>";
            echo "<td>";
            echo "<select name='rolec'>";
            echo "<option value='admin'" . ($prsn['rolec'] == 'admin' ? ' selected' : '') . ">Admin</option>";
            echo "<option value='user'" . ($prsn['rolec'] == 'user' ? ' selected' : '') . ">User</option>";
            echo "<option value='waiting'" . ($prsn['rolec'] == 'waiting' ? ' selected' : '') . ">Waiting</option>";
            echo "<option value='reject'" . ($prsn['rolec'] == 'reject' ? ' selected' : '') . ">Rejeté</option>";
            echo "<option value='accept'" . ($prsn['rolec'] == 'accept' ? ' selected' : '') . ">Accepté</option>";
            echo "</select>";
            echo "</td>";
            echo "<td><input type='password' name='psswd' value='" . htmlspecialchars($prsn['psswd']) . "'></td>";
        
            if ($prsn['rolec'] === 'waiting') {
                echo "<td><input type='hidden' name='id' value='" . htmlspecialchars($prsn['id']) . "'><button type='submit' name='accept'>Valider</button></td>";
                echo "<td><input type='hidden' name='id' value='" . htmlspecialchars($prsn['id']) . "'><button type='submit' name='denied'>Décliner</button></td>";
            } else {
                echo "<td><input type='hidden' name='id' value='" . htmlspecialchars($prsn['id']) . "'><button type='submit' name='update'>Mettre à jour</button></td>";
        
                if ($loan_count == 0) {
                    echo "<td><button type='submit' name='delete' value='" . htmlspecialchars($prsn['id']) . "'>Supprimer</button></td>";
                } else {
                    echo "<td>Emprunts en cours</td>";
                }
            }
            echo "</form>";
            echo "</tr>";
        }
        echo "</table>";
    }
    /*-------------------------------------------------ANNONCES--------------------------------------------*/
    // Logique pour gérer les annonces
    elseif ($_SESSION['active_section'] === 'annonces') {
        $image_directory = "imageanno";
        // Suppression de l'annonce
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
            $id = $_POST['delete'];
            $sql = "DELETE FROM annonce WHERE id = :id";
            $reqprep = $pdo->prepare($sql);
            $reqprep->execute(array('id' => $id));
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        // Mise à jour de annonce
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
            $id = $_POST['id'];
            $titre = $_POST['titre'];
            if ($_POST['picture'] != '') {
                $picture = $_POST['picture'];
            }
            $auteur = $_POST['auteur'];
            $editeur = $_POST['editeur'];
            $dateparution = $_POST['dateparution'];
            $genre = $_POST['genre'];
            $stock = $_POST['stock'];

            // Récupérer le chemin de l'image de l'annonce existante à partir de la base de données
            if (!isset($picture)) {
                $sql = "SELECT picture FROM annonce WHERE id = :id";
                $reqprep = $pdo->prepare($sql);
                $reqprep->execute(array('id' => $id));
                $annonce = $reqprep->fetch(PDO::FETCH_ASSOC);
                $picture = $annonce['picture'];
            } else {
                $picture = $image_directory . '/' . $picture;
            }
            // Mettre à jour l'annonce en utilisant le chemin de l'image existante
            $sql = "UPDATE annonce SET titre = :titre, picture = :picture, auteur = :auteur, editeur = :editeur, dateparution = :dateparution, genre = :genre, stock = :stock WHERE id = :id";
            $reqprep = $pdo->prepare($sql);
            $reqprep->execute(
                array(
                    'titre' => $titre,
                    'picture' => $picture, // Utilisez le chemin d'image existant
                    'auteur' => $auteur,
                    'editeur' => $editeur,
                    'dateparution' => $dateparution,
                    'genre' => $genre,
                    'stock' => $stock,
                    'id' => $id
                )
            );

            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }

        // Ajout d'une nouvelle annonce
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_book'])) {
            // Vérifie si le dossier existe, sinon le crée
            if (!file_exists($image_directory)) {
                mkdir($image_directory, 0777, true); // Crée le dossier récursivement avec les permissions 0777
                if (!file_exists($image_directory)) {
                    header('Location :error.php');
                    exit; // Arrêter l'exécution du script en cas d'échec de création du dossier
                }
            }

            // Vérification du type de fichier avec exif_imagetype
            $allowed_types = array(IMAGETYPE_JPEG, IMAGETYPE_PNG);
            $uploaded_type = exif_imagetype($_FILES['picture']['tmp_name']);
            if (!in_array($uploaded_type, $allowed_types)) {
                header('Location: error.php');
                exit; // Arrêter l'exécution du script si le type de fichier n'est pas autorisé
            }

            // Traitement de l'upload de l'image
            if (isset($_FILES['picture']) && is_uploaded_file($_FILES['picture']['tmp_name'])) {
                $orig = $_FILES['picture']['tmp_name'];
                $dest = $image_directory . '/' . $_FILES['picture']['name'];
                move_uploaded_file($orig, $dest);
                $new_titre = $_POST['titre'];
                $new_auteur = $_POST['auteur'];
                $new_editeur = $_POST['editeur'];
                $new_dateparution = $_POST['dateparution'];
                $new_genre = $_POST['genre'];
                $new_stock = $_POST['stock'];
                $sql = "INSERT INTO annonce (titre, picture, auteur, editeur, dateparution, genre, stock) VALUES (:titre, :picture, :auteur, :editeur, :dateparution, :genre, :stock)";
                $reqprep = $pdo->prepare($sql);
                $reqprep->execute(
                    array(
                        'titre' => $new_titre,
                        'picture' => $dest,
                        'auteur' => $new_auteur,
                        'editeur' => $new_editeur,
                        'dateparution' => $new_dateparution,
                        'genre' => $new_genre,
                        'stock' => $new_stock
                    )
                );
            }
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        }


        // Sélection des annonces depuis la table 'annonce'
        $sql = "SELECT * FROM annonce";
        $reqprep = $pdo->prepare($sql);
        $reqprep->execute();

        // Barre de navigation
        echo "<nav>";
        echo "<button onclick=\"window.location.href='main.php'\">Menu</button>";
        echo "<form method='post'>";
        echo "<button type='submit' name='section' value='comptes'>Gérer les comptes</button>";
        echo "<button type='submit' name='section' value='emprunts'>Gérer les emprunts</button>";
        echo "</form>";
        // Formulaire de déconnexion
        echo "<form id=dec action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
        echo "<button type='submit' name='logout'>Déconnexion</button>";
        echo "</form>";
        echo "<div class='animation start-home'></div>";
        echo "</nav>";


        // Formulaire d'ajout d'une annonce
        echo "<div class=form_compte>";
        echo "<h2 class=ajout >Ajouter un livre</h2>";
        echo "<form class='infos' method='post' enctype='multipart/form-data'>";
        echo "Titre:<br>";
        echo "<input type='text' name='titre' required><br>";
        echo "Auteur:<br>";
        echo "<input type='text' name='auteur'required><br>";
        echo "Image :<br>";
        echo "<input type='file' name='picture' accept='image/png, image/jpeg, image/jpg' required/><br>";
        echo "Editeur:<br>";
        echo "<input type='text' name='editeur'required><br>";
        echo "Date de parution:<br>";
        echo "<input type='date' name='dateparution' max='" . date('Y-m-d') . "' required><br>";
        echo "Genre:<br>";
        echo "<input type='text' name='genre' required><br>";
        echo "Stock:<br>";
        echo "<input type='number' name='stock' min='0' required><br>";
        echo "<button type='submit' name='add_book'>Ajouter</button>";
        echo "</form>";
        echo "</div>";
        
        echo "<script>
        function removeAccents() {
            var accentMap = {
                'à': 'a', 'â': 'a', 'ä': 'a', 'á': 'a', 'ã': 'a', 'å': 'a', 
                'è': 'e', 'é': 'e', 'ê': 'e', 'ë': 'e',
                'ì': 'i', 'í': 'i', 'î': 'i', 'ï': 'i',
                'ò': 'o', 'ó': 'o', 'ô': 'o', 'ö': 'o', 'õ': 'o',
                'ù': 'u', 'ú': 'u', 'û': 'u', 'ü': 'u',
                'ý': 'y', 'ÿ': 'y',
                'ç': 'c', 'ñ': 'n'
            };
        
            var form = document.getElementById('ajoutLivreForm');
            var elements = form.elements;
        
            for (var i = 0; i < elements.length; i++) {
                if (elements[i].type === 'text' || elements[i].type === 'textarea') {
                    var fieldValue = elements[i].value;
                    var newValue = '';
                    for (var j = 0; j < fieldValue.length; j++) {
                        var char = fieldValue[j];
                        newValue += accentMap[char] || char;
                    }
                    elements[i].value = newValue;
                }
            }
        }
        </script>";
        
        // Affichage des annonces
        echo "<h1 class='annonces'>Gérer les annonces</h1>";
        echo "<table class='table_input'>";
        echo "<tr><th>ID</th><th>Titre</th><th>Auteur</th><th>Editeur</th><th>Date de parution</th><th>Genre</th><th>Stock</th><th>Image</th><th colspan='3'>Action</th></tr>";

        while ($annonce = $reqprep->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<form method='post'>"; // Formulaire de mise à jour individuel
            echo "<td>" . $annonce['id'] . "</td>";
            echo "<td><input type='text' name='titre' value='" . $annonce['titre'] . "'></td>";
            echo "<td><input type='text' name='auteur' value='" . $annonce['auteur'] . "'></td>";
            echo "<td><input type='text' name='editeur' value='" . $annonce['editeur'] . "'></td>";
            echo "<td><input type='date' name='dateparution' value='" . $annonce['dateparution'] . "' max='" . date('Y-m-d') . "'></td>";
            echo "<td><input type='text' name='genre' value='" . $annonce['genre'] . "'></td>";
            echo "<td><input type='number' name='stock' value='" . $annonce['stock'] . "' min='0'></td>";
            echo "<td><div class='thumbnail-container'><img class='thumbnail' src='" . $annonce['picture'] . "' alt='Image' width='50' height='50'></div></td>"; // Utilisation d'une div comme conteneur
            echo "<td><input type='file' name='picture'" . $annonce['picture'] . "' accept='image/png, image/jpeg, image/jpg'></td>";
            echo "<td><input type='hidden' name='id' value='" . $annonce['id'] . "'><button type='submit' name='update'>Mettre à jour</button></td>";
            echo "<td><button type='submit' name='delete' value='" . $annonce['id'] . "'>Supprimer</button></td>";
            echo "</form>";
            echo "</tr>";
        }
        echo "</table>";
    }
    //------------------------------------EMPRUNTS-------------------------------------
    elseif ($_SESSION['active_section'] === 'emprunts') {
        // Traitement des actions selon les requêtes reçues

        // Si la requête est pour valider une prolongation
        if (isset($_POST['valider_p'])) {
            $idloan = $_POST['valider_p'];
            $sql = "UPDATE emprunt SET datefin = DATE_ADD(datefin, INTERVAL 7 DAY), statut_loan = 'Emprunt', nb_prolongations = nb_prolongations + 1 WHERE idloan = :idloan";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['idloan' => $idloan]);
        }

        // Si la requête est pour décliner une prolongation
        if (isset($_POST['decliner_p'])) {
            $idloan = $_POST['decliner_p'];
            $sql = "UPDATE emprunt SET statut_loan = 'Emprunt' ,nb_prolongations = -1 WHERE idloan = :idloan";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['idloan' => $idloan]);
        }

        // Si la requête est pour valider un retour d'emprunt
        if (isset($_POST['valider'])) {
            $idloan = $_POST['valider'];
            // Récupérer l'id du livre associé à l'emprunt
            $sql_get_book = "SELECT idbook FROM emprunt WHERE idloan = :idloan";
            $req_get_book = $pdo->prepare($sql_get_book);
            $req_get_book->execute(['idloan' => $idloan]);
            $book = $req_get_book->fetch(PDO::FETCH_ASSOC);
            $idbook = $book['idbook'];
            // Mettre à jour le stock du livre
            $sql_update_stock = "UPDATE annonce SET stock = stock + 1 WHERE id = :idbook";
            $req_update_stock = $pdo->prepare($sql_update_stock);
            $req_update_stock->execute(['idbook' => $idbook]);
    
            // Supprimer l'emprunt
            $sql = "DELETE FROM emprunt WHERE idloan = :idloan";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['idloan' => $idloan]);
        }

        // Si la requête est pour décliner un retour d'emprunt
        if (isset($_POST['decliner'])) {
            $idloan = $_POST['decliner'];
            $sql = "UPDATE emprunt SET statut_loan = 'Emprunt' WHERE idloan = :idloan";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['idloan' => $idloan]);
        }

        // Affichage de la liste des emprunts
        // Barre de navigation
        echo "<nav>";
        echo "<button onclick=\"window.location.href='main.php'\">Menu</button>";
        echo "<form method='post'>";
        echo "<button type='submit' name='section' value='comptes'>Gérer les comptes</button>";
        echo "<button type='submit' name='section' value='annonces'>Gérer les annonces</button>";
        echo "</form>";
        // Formulaire de déconnexion
        echo "<form  id=dec2 action='" . $_SERVER['PHP_SELF'] . "' method='post'>";
        echo "<button  type='submit' name='logout'>Déconnexion</button>";
        echo "</form>";
        echo "<div class='animation start-home'></div>";
        echo "</nav>";
        echo "<h1 class='emprunts'>Gérer les emprunts</h1>";
        echo "<form method='post'>";
        echo "<table>";
        echo "<tr><th>ID Emprunt</th><th>Titre du Livre</th><th>Emprunteur</th><th>Date de Début</th><th>Date de Fin</th><th>Prolongations</th><th>Statut</th><th colspan='2'>Actions</th></tr>";
        // Sélection des emprunts depuis la table 'emprunt'
        $sql_emprunts = "SELECT e.idloan, a.titre, CONCAT(c.prenom, ' ', c.nom) AS emprunteur, e.datedebut, e.datefin, e.nb_prolongations, e.statut_loan 
                            FROM emprunt e 
                            INNER JOIN annonce a ON e.idbook = a.id
                            INNER JOIN compte c ON e.iduser = c.id
                            WHERE e.statut_loan != 'panier'";;
        $req_emprunts = $pdo->prepare($sql_emprunts);
        $req_emprunts->execute();

        while ($emprunt = $req_emprunts->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>" . $emprunt['idloan'] . "</td>";
            echo "<td>" . $emprunt['titre'] . "</td>";
            echo "<td>" . $emprunt['emprunteur'] . "</td>";
            echo "<td>" . $emprunt['datedebut'] . "</td>";
            echo "<td>" . $emprunt['datefin'] . "</td>";
            echo "<td>" . $emprunt['nb_prolongations'] . "</td>";
            echo "<td>" . $emprunt['statut_loan'] . "</td>";
            // Conditions pour afficher les cases à cocher ou les boutons
            if ($emprunt['statut_loan'] === 'waiting') {
                echo "<td colspan='2'><p>Demande de prolongation</p><button type='submit' name='valider_p' value='" . $emprunt['idloan'] . "'>Valider</button><button type='submit' name='decliner_p' value='" . $emprunt['idloan'] . "'>Décliner</button></td>";
            } elseif ($emprunt['statut_loan'] === 'ret_waiting') {
                echo "<td colspan='2'><p>Livre Rendu</p><button type='submit' name='valider' value='" . $emprunt['idloan'] . "'>Valider</button><button type='submit' name='decliner' value='" . $emprunt['idloan'] . "'>Décliner</button></td>";
            } else {
                echo "<td colspan='2'>Aucune demande</td>";
            }
            echo "</tr>";
        }
        echo "</form>";
        echo "</table>";
    }
    ?>
        <?php include 'footer.php'; ?>

    </body>

</html>