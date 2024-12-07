<?php
session_start();
// Génération du jeton CSRF
$token = bin2hex(random_bytes(32)); // Générez un jeton aléatoire de 32 octets

// Enregistrement du jeton CSRF dans la variable de session
$_SESSION['token'] = $token;

require_once('config.php'); // Inclusion du fichier de configuration

$session_timeout = 600; // 300 secondes = 5 minutes

if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > $session_timeout) {
    session_destroy();
    header('Location: connect.php');
    exit;
} else {
    $_SESSION['last_activity'] = time();
}


if (isset($_SESSION['email'])) {
    header('Location: profil.php');
} else {
    // Formulaire de connexion
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $email = htmlspecialchars($_POST['email']);
        $psswd = $_POST['psswd'];
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT * FROM compte WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user > 0 && password_verify($psswd, $user['psswd'])) {
            $_SESSION['email'] = $email;
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit;
        } else {
            $erreur = "Adresse mail ou mot de passe incorrect.";
        }
    }

?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="css/footer.css" />
        <title>Connexion</title>
    </head>

    <body>
    <div class="container" id="container">
     <div class="form-container sign-in-container">
        <?php if (isset($erreur)) echo "<p style='color: red; text-align: center; margin-top: 10px'>" . htmlspecialchars($erreur) . "</p>";
        echo "<link rel='stylesheet' href='css/cnx.css' />"; ?>
        <form id="connect" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <h1>Connexion</h1>
        <div>
                <label for="email">Adresse mail</label><br>
                <input type="email" id="email" name="email" required><br>
                <label for="psswd">Mot de passe</label><br>
                <input type="password" id="psswd" name="psswd" required><br><br>
                <button type="submit">Se connecter</button>
            </div>
        </form>
        </div>
        <br>
        <div class="form-container sign-up-container">
        <form id="register" action="form.php" method="post">
            <h1>Inscription</h1>
            <label for="prenom">Prénom</label><br>
            <input type="text" name="prenom" id="prenom" required><br>
            <label for="nom">Nom</label><br>
            <input type="text" name="nom" id="nom" required><br>
            <label for="birth">Date de naissance</label><br>
            <input type="date" name="birth" id="birth" max="<?php echo date('Y-m-d'); ?>" required><br>
            <label for="email">Email</label><br>
            <input type="email" name="email"  required><br>
            <label for="mdpuser">Mot de passe</label><br>
            <input type="password" name="mdpuser" id="mdpuser" required><br>
            <!-- Ajout d'un jeton CSRF via javascript -->
            <button type="submit">Valider</button>
        </form>
        </div>
        <div class="overlay-container">
          <div class="overlay">
            <div class="overlay-panel overlay-left">
            <h1>De Retour !</h1>
              <button class="ghost" id="signIn">Se Connecter</button>
              <a href="main.php">Retour à la page d'accueil</a>
            </div>
            <div class="overlay-panel overlay-right">
            <h1>Bienvenue mon ami!</h1>
              <button class="ghost" id="signUp">S'inscrire</button>
              <a href="main.php">Retour à la page d'accueil</a>
            </div>
          </div>
        </div>
    </div>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                var csrfToken = $token;
                var form = document.getElementById("register");
                var tconn = document.getElementById("connect");
                var input = document.createElement("input");
                input.type = "hidden";
                input.name = "token";
                input.value = csrfToken;
                form.appendChild(input);
                tconn.appendChild(input);

            });
            
            window.addEventListener('load', function() {
                const signUpButton = document.getElementById('signUp');
                const signInButton = document.getElementById('signIn');
                const container = document.getElementById('container');
                
                signUpButton.addEventListener('click', () => {
                    container.classList.add("right-panel-active");
                });
                
                signInButton.addEventListener('click', () => {
                    container.classList.remove("right-panel-active");
                });
            });
        </script>
<?php
}
?>

<?php include 'footer.php'; ?>
    </body>
    </html>