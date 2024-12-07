<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flappy Bird</title>
    <link rel="stylesheet" href="css/style.css">
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const backgroundImages = [
                'url(../source/wall1.jpg)',
                'url(../source/wall2.jpg)',
                'url(../source/wall3.jpg)',
                'url(../source/wall4.jpg)',
                'url(../source/wall5.jpg)',
                'url(../source/wall6.jpg)',
                'url(../source/wall7.jpg)'
            ];
            const bird = document.getElementById('bird');
            const gameContainer = document.getElementById('game-container');
            const pipe = document.getElementById('pipe');
            const pipe2 = document.getElementById('pipe2');
            const gameOverMessage = document.getElementById('game-over-message');
            const scoreDisplay = document.getElementById('score');
            const finalScore = document.getElementById('final-score');
            const restartButton = document.getElementById('restart-button');
            const countdownDisplay = document.getElementById('countdown');

            let birdTop = 250;
            let birdSpeed = 0;
            let gravity = 0.5;
            let jumpStrength = -8;
            let isGameOver = false;
            let pipeSpeed = 2;
            let gap = 150;
            let pipeLeft = 500;
            let score = 0;

            function startGame() {
                countdown(3, startActualGame);
            }

            function startActualGame() {
                document.addEventListener('keydown', jump);
                requestAnimationFrame(updateGame);
            }

            // Variable pour la vitesse initiale des tuyaux
            let initialPipeSpeed = 1;
            let pipeAcceleration = 0.1; //  Valeur d'accélération

            function updateGame() {
                if (!isGameOver) {
                    birdSpeed += gravity;
                    birdTop += birdSpeed;
                    bird.style.top = birdTop + 'px';
                    bird.style.transform = `rotate(${birdSpeed * 3}deg)`;
                    movePipes();
                    checkCollision();
                    score++;
                    scoreDisplay.textContent = `Score: ${score}`;

                    let targetSpeed = initialPipeSpeed + Math.floor(score / 500) * 1.1; // Vitesse cible des tuyaux
                    pipeSpeed += (targetSpeed - pipeSpeed) * 0.1;

                    // Vérifie si le score est un multiple de 500
                    if (score % 500 === 0) {
                        // Sélectionne aléatoirement une image de fond de la liste
                        const randomIndex = Math.floor(Math.random() * backgroundImages.length);
                        const backgroundImage = backgroundImages[randomIndex];
                        // Applique l'image de fond sélectionnée au game-container
                        gameContainer.style.backgroundImage = backgroundImage;
                    }

                    requestAnimationFrame(updateGame);
                }
            }

            function jump() {
                birdSpeed = jumpStrength;
            }

            function movePipes() {
                pipeLeft -= pipeSpeed;
                if (pipeLeft < -60) {
                    pipeLeft = 500;
                    let pipeHeight = Math.random() * 200 + 50;
                    pipe.style.height = pipeHeight + 'px';
                    pipe2.style.height = (950 - pipeHeight - gap) + 'px';
                }
                pipe.style.left = pipeLeft + 'px';
                pipe2.style.left = pipeLeft + 'px';
            }

            function checkCollision() {
                const birdRect = bird.getBoundingClientRect();
                const pipeRect = pipe.getBoundingClientRect();
                const pipe2Rect = pipe2.getBoundingClientRect();
                const gameContainerRect = gameContainer.getBoundingClientRect();

                // Vérification des collisions avec le top et bottom du game container
                if (birdRect.top <= gameContainerRect.top || birdRect.bottom >= gameContainerRect.bottom) {
                    endGame();
                    return;
                }

                // Vérification des collisions avec les pipes
                if (
                    (birdRect.right > pipeRect.left && birdRect.left < pipeRect.right &&
                        birdRect.bottom > pipeRect.top && birdRect.top < pipeRect.bottom) ||
                    (birdRect.right > pipe2Rect.left && birdRect.left < pipe2Rect.right &&
                        birdRect.bottom > pipe2Rect.top && birdRect.top < pipe2Rect.bottom)
                ) {
                    endGame();
                    return;
                }
            }

            function endGame() {
                isGameOver = true;
                gameOverMessage.style.display = 'block';
                finalScore.textContent = `Votre score: ${score}`;
                document.getElementById('scoreInput').value = score;
            }

            restartButton.addEventListener('click', () => {
                location.reload();
            });

            function countdown(seconds, callback) {
                countdownDisplay.style.display = 'block';
                countdownDisplay.textContent = seconds;
                if (seconds > 0) {
                    setTimeout(() => {
                        countdown(seconds - 1, callback);
                    }, 1000);
                } else {
                    countdownDisplay.style.display = 'none';
                    callback();
                }
            }
            startGame();
        });
    </script>
</head>

<body>
    <form id="scoreForm" method="POST" action="process_score.php" style="display:none;">
        <input type="hidden" name="score" id="scoreInput">
    </form>
    <div id="game-container" class="game-background">
        <img id="bird" src="../source/logo.PNG" alt="Bird">
        <div id="pipe"></div>
        <div id="pipe2"></div>
        <div id="score">Score: 0</div>
        <div id="game-over-message">
            <p>Game Over</p>
            <p id="final-score"></p>
            <button id="restart-button">Rejouer</button>
            <a href="main.php" class="exit-button">Exit</a>
            <a href="main.php" class="menu-button">Menu</a>
        </div>
        <div id="countdown"></div>
    </div>
</body>

</html>