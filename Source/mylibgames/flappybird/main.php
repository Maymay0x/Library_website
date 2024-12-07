<?php
// Vérifie si le fichier scores.txt existe, sinon le créer
$file = 'scores.txt';
if (!file_exists($file)) {
    // Créer le fichier scores.txt
    if (!touch($file)) {
        die('Impossible de créer le fichier des scores.');
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyLibJump - Menu</title>
    <link rel="stylesheet" href="css/main.css">
    <script>
        document.addEventListener('keydown', function(event) {
            if (event.code === 'Space') {
                window.location.href = 'flappy.php';
            }
        });
    </script>
</head>

<body>
    <div class="menu-container">
        <div class="content">
            <h1 class="menu-title">MyLibJump</h1>
            <div class="scoreboard">
                <h2>Top 5 Classements</h2>
                <ul>
                    <?php
                    // Lire les scores depuis le fichier scores.txt et les afficher
                    $scores = file('scores.txt');
                    if ($scores !== false) {
                        rsort($scores); // Trier les scores dans l'ordre décroissant
                        $count = min(5, count($scores)); // Limiter à afficher seulement les 5 premiers scores
                        for ($i = 0; $i < $count; $i++) {
                            echo "<li>{$scores[$i]}</li>";
                        }
                    } else {
                        echo "<li>Aucun score disponible.</li>";
                    }
                    ?>
                </ul>
            </div>
            <p class="instruction blink">Press space to discover a new universe</p>
            <a href="../../../main.php" class="menu-button">Menu</a>
            <footer>
                <p>&copy; 2024 MyLibGames</p>
            </footer>
        </div>
    </div>
</body>

</html>