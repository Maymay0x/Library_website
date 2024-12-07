<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['score'])) {
        $file = 'scores.txt';
        // Vérifier si le fichier existe
        if (!file_exists($file)) {
            // Créer le fichier s'il n'existe pas
            if (!touch($file)) {
                die('Impossible de créer le fichier des scores.');
            }
        }
        // Enregistrer le score dans le fichier
        if (file_put_contents($file, $score . PHP_EOL, FILE_APPEND) !== false) {
            echo 'Score enregistré avec succès.';
        } else {
            echo 'Une erreur s\'est produite lors de l\'enregistrement du score.';
        }
    }
}
