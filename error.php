<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/err.css" />
    <title>inscription</title>
</head>

<body>
    <?php
    if (isset($_SESSION['decide'])) {
        if ($_SESSION['decide'] === "wait") {
            echo "<div class='retour'>";
            echo "<p>Votre inscription est en cours de traitement.</p>";
            echo "</div>";
        } elseif ($_SESSION['decide'] === "reject") {
            echo "<div class='retour'>";
            echo "<p>Votre inscription à été refusée.</p>";
        }
        session_destroy();
    ?>
        <a href="connect.php">Retour au formulaire</a>
        </div>
    <?php
    } else {
        header('Location: connect.php');
    }
    ?>
</body>

</html>