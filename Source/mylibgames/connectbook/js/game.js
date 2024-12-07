const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d');

canvas.width = 800;
canvas.height = 600;

const profileSize = 20;
let profiles = [];

let connections = [];
let connecting = false;
let startProfile = null;
let endProfile = null;

let score = 0;
let lives = 5;

let profileGenerationRunning = true;
let profileGenerationInterval = 1000;
let profileGenerationIntervalId;

profileGenerationIntervalId = setInterval(addRandomProfile, profileGenerationInterval);

function adjustProfileGenerationInterval() {
    clearInterval(profileGenerationIntervalId);
    profileGenerationIntervalId = setInterval(addRandomProfile, profileGenerationInterval);
}

function addRandomProfile() {
    if (!profileGenerationRunning) return;
    profileGenerationInterval -= 5;
    if (profileGenerationInterval < 200) {
        profileGenerationInterval = 200;
    }
    let newProfile;
    let attempts = 0;
    do {
        newProfile = {
            x: Math.random() * (canvas.width - profileSize * 2) + profileSize,
            y: Math.random() * (canvas.height - profileSize * 2) + profileSize
        };
        attempts++;
        if (attempts > 1000) {
            gameOver();
            return;
        }
    } while (isProfileOverlapping(newProfile));
    profiles.push(newProfile);
    adjustProfileGenerationInterval();
    draw();
}

let gameRunning = true;

canvas.addEventListener('mousedown', (e) => {
    if (!gameRunning) return;

    const mousePos = getMousePos(canvas, e);
    for (const [index, profile] of profiles.entries()) {
        if (isInside(mousePos, profile)) {
            connecting = true;
            startProfile = profile;
            endProfile = mousePos;
            draw();
            break;
        }
    }
});

canvas.addEventListener('mousemove', (e) => {
    if (!gameRunning) return;

    if (connecting) {
        endProfile = getMousePos(canvas, e);
        draw();
    }
});

canvas.addEventListener('mouseup', (e) => {
    if (!gameRunning) return;

    if (connecting) {
        const mousePos = getMousePos(canvas, e);
        let connectedProfile = null;
        for (const profile of profiles) {
            if (isInside(mousePos, profile) && profile !== startProfile) {
                connectedProfile = profile;
                break;
            }
        }
        if (connectedProfile !== null) {
            connections.push({ start: startProfile, end: connectedProfile });
            profiles = profiles.filter(profile => profile !== connectedProfile && !isProfileConnected(profile));
            connections = connections.filter(connection => connection.start !== connectedProfile && connection.end !== connectedProfile);
            score++;
            document.getElementById('score').innerText = `Score: ${score}`;
        } else {
            loseLife();
        }
        connecting = false;
        startProfile = null;
        endProfile = null;
        draw();
    }
});

function isProfileConnected(profile) {
    for (const connection of connections) {
        if (connection.start === profile || connection.end === profile) {
            return true;
        }
    }
    return false;
}

function isProfileOverlapping(newProfile) {
    for (const profile of profiles) {
        const distance = Math.sqrt((newProfile.x - profile.x) ** 2 + (newProfile.y - profile.y) ** 2);
        if (distance < profileSize * 2) {
            return true;
        }
    }
    return false;
}

function getMousePos(canvas, evt) {
    const rect = canvas.getBoundingClientRect();
    return {
        x: evt.clientX - rect.left,
        y: evt.clientY - rect.top
    };
}

function isInside(pos, profile) {
    return pos.x > profile.x - profileSize && pos.x < profile.x + profileSize &&
        pos.y > profile.y - profileSize && pos.y < profile.y + profileSize;
}
const image = new Image();
image.src = '../source/logo.PNG';
image.onload = function () {
    drawProfiles();
};
function drawProfiles() {
    for (const profile of profiles) {
        ctx.save();
        ctx.beginPath();
        ctx.arc(profile.x, profile.y, profileSize, 0, 2 * Math.PI);
        ctx.clip();
        ctx.drawImage(image, profile.x - profileSize, profile.y - profileSize, profileSize * 2, profileSize * 2);
        ctx.restore();
    }

}


function drawConnections() {
    for (const connection of connections) {
        ctx.beginPath();
        ctx.moveTo(connection.start.x, connection.start.y);
        ctx.lineTo(connection.end.x, connection.end.y);
        ctx.strokeStyle = '#42b72a';
        ctx.lineWidth = 2;
        ctx.stroke();
    }
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawConnections();
    drawProfiles();
    drawLives();
    if (connecting && startProfile !== null && endProfile !== null) {
        ctx.beginPath();
        ctx.moveTo(startProfile.x, startProfile.y);
        ctx.lineTo(endProfile.x, endProfile.y);
        ctx.strokeStyle = '#42b72a';
        ctx.lineWidth = 2;
        ctx.stroke();
    }
}

function drawLives() {
    const heartWidth = 30;
    const heartHeight = 30;
    const heartPadding = 10;
    const heartColor = '#ff0000';
    const grayHeartColor = '#808080';
    const heartContainer = document.getElementById('lives');
    heartContainer.innerHTML = '';

    for (let i = 0; i < lives; i++) {
        const heartSpan = document.createElement('span');
        heartSpan.style.marginRight = `${heartPadding}px`;
        heartSpan.innerHTML = '&hearts;';
        heartSpan.style.color = i < lives ? heartColor : grayHeartColor;
        heartContainer.appendChild(heartSpan);
    }
}


function drawHeart(x, y, size, color) {
    ctx.fillStyle = color;
    ctx.beginPath();
    ctx.moveTo(x, y + size / 4);
    ctx.quadraticCurveTo(x, y, x + size / 4, y);
    ctx.quadraticCurveTo(x + size / 2, y, x + size / 2, y + size / 4);
    ctx.quadraticCurveTo(x + size / 2, y, x + size * 3 / 4, y);
    ctx.quadraticCurveTo(x + size, y, x + size, y + size / 4);
    ctx.quadraticCurveTo(x + size, y + size / 2, x + size * 3 / 4, y + size * 3 / 4);
    ctx.lineTo(x + size / 2, y + size);
    ctx.lineTo(x + size / 4, y + size * 3 / 4);
    ctx.quadraticCurveTo(x, y + size / 2, x, y + size / 4);
    ctx.fill();
}



function loseLife() {
    lives--;
    if (lives === 0) {
        gameOver();
    }
}

function gameOver() {
    gameRunning = false;
    profileGenerationRunning = false;
    const modal = document.createElement('div');
    modal.classList.add('modal');
    modal.innerHTML = `
        <div class="modal-content">
            <h1>Game Over</h1>
            <p>Your score: ${score}</p>
            <button onclick="restartGame()">Rejouer</button>
            <button onclick="backToMenu()">Menu</button>
        </div>
    `;
    document.body.appendChild(modal);
}

function backToMenu() {
    window.location.href = "../../../main.php";
}

function restartGame() {
    // Réinitialisation du jeu
    profiles = [];
    connections = [];
    score = 0;
    profileGenerationInterval = 1000;
    lives = 5; // Réinitialisation du nombre de vies
    gameRunning = true; // Réinitialisez l'état du jeu
    profileGenerationRunning = true; // Réinitialisez l'état de la génération de profils
    document.getElementById('score').innerText = `Score: ${score}`;
    addRandomProfile(); // Ajouter un premier profil
    const modal = document.querySelector('.modal');
    if (modal) {
        modal.remove();
    }
    draw();
}

draw();

