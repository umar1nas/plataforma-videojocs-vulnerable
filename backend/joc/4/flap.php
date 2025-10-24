<?php
session_start();
// Si no hay sesión, asignamos un usuario por defecto (inseguro a propósito)
if (!isset($_SESSION['usuari_id'])) {
    $_SESSION['usuari_id'] = 1;
}
$session_user = $_SESSION['usuari_id'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Flappy Bird Vulnerable</title>
    <style>
        body {
            background: #70c5ce;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            height: 100vh;
            margin: 0;
            overflow: hidden;
            font-family: Arial, sans-serif;
        }

        h1 {
            color: white;
            text-shadow: 2px 2px 4px #000;
            margin-top: 20px;
        }

        #gameCanvas {
            background: #4ec0ca;
            border: 3px solid #333;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.3);
        }

        #score {
            color: #fff;
            font-size: 24px;
            margin-top: 10px;
        }

        #gameOverOverlay {
            display: none;
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.6);
            align-items: center;
            justify-content: center;
            flex-direction: column;
            color: white;
            text-align: center;
        }

        #gameOverOverlay.active {
            display: flex;
        }

        button {
            margin-top: 10px;
            padding: 10px 20px;
            border: none;
            background: #ffcc00;
            font-weight: bold;
            border-radius: 8px;
            cursor: pointer;
        }
        button:hover {
            background: #ffdd33;
        }
    </style>
</head>
<body>

<h1>🐤 Flappy Bird (versión vulnerable)</h1>
<canvas id="gameCanvas" width="400" height="512"></canvas>
<div id="score">Puntuación: 0</div>

<div id="gameOverOverlay">
    <h2>💀 ¡Game Over!</h2>
    <p>Puntuación final: <span id="finalScore">0</span></p>
    <button onclick="startGame()">Jugar de nuevo</button>
</div>

<script>
// =================== CONFIGURACIÓN USUARIO ===================
function qs(param) {
    const urlParams = new URLSearchParams(window.location.search);
    return urlParams.get(param);
}

const USER_ID = qs('usuari_id') ? qs('usuari_id') : <?php echo json_encode($session_user); ?>;
const GAME_ID = 4; // ID del juego Flappy Bird
// =============================================================

// ========= JUEGO FLAPPY BIRD (Simplificado) ==================
const canvas = document.getElementById("gameCanvas");
const ctx = canvas.getContext("2d");

let bird = { x: 50, y: 150, w: 30, h: 30, gravity: 0.6, lift: -10, velocity: 0 };
let pipes = [];
let frame = 0;
let score = 0;
let gameOverFlag = false;

document.addEventListener("keydown", flap);
canvas.addEventListener("click", flap);

function flap() {
    bird.velocity = bird.lift;
}

function startGame() {
    document.getElementById("gameOverOverlay").classList.remove("active");
    bird.y = 150;
    bird.velocity = 0;
    pipes = [];
    frame = 0;
    score = 0;
    gameOverFlag = false;
    loop();
}

function drawBird() {
    ctx.fillStyle = "yellow";
    ctx.fillRect(bird.x, bird.y, bird.w, bird.h);
}

function drawPipes() {
    ctx.fillStyle = "green";
    pipes.forEach(pipe => {
        ctx.fillRect(pipe.x, 0, pipe.w, pipe.top);
        ctx.fillRect(pipe.x, pipe.top + pipe.gap, pipe.w, canvas.height - pipe.top - pipe.gap);
    });
}

function updatePipes() {
    if (frame % 100 === 0) {
        const top = Math.random() * (canvas.height / 2);
        pipes.push({ x: canvas.width, w: 40, top, gap: 120 });
    }
    pipes.forEach(pipe => pipe.x -= 2);
    pipes = pipes.filter(pipe => pipe.x + pipe.w > 0);
}

function checkCollision() {
    for (const pipe of pipes) {
        if (
            bird.x < pipe.x + pipe.w &&
            bird.x + bird.w > pipe.x &&
            (bird.y < pipe.top || bird.y + bird.h > pipe.top + pipe.gap)
        ) {
            return true;
        }
    }
    if (bird.y + bird.h > canvas.height || bird.y < 0) {
        return true;
    }
    return false;
}

function update() {
    bird.velocity += bird.gravity;
    bird.y += bird.velocity;
    updatePipes();

    // puntuación
    pipes.forEach(pipe => {
        if (pipe.x + pipe.w === bird.x) {
            score++;
        }
    });

    if (checkCollision()) {
        gameOver();
    }
}

function draw() {
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    drawBird();
    drawPipes();
    ctx.fillStyle = "white";
    ctx.font = "20px Arial";
    ctx.fillText("Puntuación: " + score, 10, 25);
}

function loop() {
    if (gameOverFlag) return;
    update();
    draw();
    frame++;
    document.getElementById("score").textContent = "Puntuación: " + score;
    requestAnimationFrame(loop);
}

function gameOver() {
    gameOverFlag = true;
    document.getElementById("finalScore").textContent = score;
    document.getElementById("gameOverOverlay").classList.add("active");

    // --- Guardado vulnerable ---
    guardarPartidaInsegura(score);
}

function guardarPartidaInsegura(puntuacion) {
    const nivell = 1;
    const durada = Math.floor(performance.now() / 1000);

    const params = `usuari_id=${USER_ID}&joc_id=${GAME_ID}&nivell=${nivell}&puntuacio=${puntuacion}&durada=${durada}`;

    // GET sin autenticación -> vulnerable a SQLi y suplantación
    fetch(`save_partida.php?${params}`, { method: "GET" })
        .then(r => r.json())
        .then(data => console.log("🧩 Respuesta vulnerable:", data))
        .catch(err => console.error("❌ Error al guardar:", err));
}

startGame();
</script>

</body>
</html>
