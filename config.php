<?php
function getConnection()
{
  

    // $host = '';
    // $dbname = '';
    // $username = '';
    // $password = '';

    $host = '';
    $dbname = '';
    $username = '';
    $password = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        // Afficher une page d'erreur stylisée en CSS
        echo "
        <!DOCTYPE html>
        <html lang='en'>
        <head>
            <meta charset='UTF-8'>
            <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            <title>Erreur</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f4f4f4;
                    margin: 0;
                    padding: 0;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .container {
                    text-align: center;
                    background-color: #fff;
                    padding: 20px;
                    border-radius: 5px;
                    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                }
                h1 {
                    color: #ff0000;
                }
            </style>
        </head>
        <body>
            <div class='container'>
                <h1>Erreur de connexion aux serveurs</h1>
                <p>Le site est actuellement indisponible. Veuillez réessayer plus tard.</p>
            </div>
        </body>
        </html>";
        exit(); // Terminer l'exécution du script après affichage de l'erreur
    }
}
