<?php
// Supongamos que el usuario ya inició sesión:
$usuario = "Jugador1"; // aquí iría $_SESSION['usuario'] o similar
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../index.php'); // o donde esté el login
    exit;
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Pong</title>
<style>
    body {
        background: #0f172a;
        color: #f8fafc;
        font-family: Arial, sans-serif;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100vh;
        overflow: hidden;
    }
    h1 { color: #6366f1; }
    canvas {
        background: #1e293b;
        border: 3px solid #6366f1;
        border-radius: 8px;
    }
    #info {
        margin-bottom: 20px;
        text-align: center;
    }
</style>
</head>
<body>
    <div id="info">
        <h1>🎮 Pong</h1>
        
    </div>
    <div class="score-board">
    <span>Puntos: <span id="score">0</span></span>
    <span>Vidas: <span id="vidas">5</span></span>
    <span>Nivel: <span id="nivel">1</span></span>
</div>

<div class="game-status">
    <div id="statusText">▶ Presiona ESPACIO para comenzar</div>
</div>

<div class="buttons-container">
    <button id="reiniciarBtn">🔄 Reiniciar</button>  
    <button id="volverMenuBtn">🏠 Volver al Menú</button>




</div>

    <canvas id="pong" width="700" height="400"></canvas>
<script>
const canvas = document.getElementById('pong');
const ctx = canvas.getContext('2d');

document.getElementById('volverMenuBtn').addEventListener('click', () => {
    // Guardar progreso antes de salir
    guardarProgreso();

    // Redirigir al menú principal
    window.location.href = "../../plataforma.php"; // Cambia esto a la URL de tu menú
});

// 🎮 Estado inicial del juego
let game = {
    running: false,
    paused: false,
    score: 0,
    level: 1,
    maxLevel: 5,
    lives: 5,
    gameOver: false,
    winScore: 12
};

// 🔐 Cargar progreso guardado
const savedData = JSON.parse(localStorage.getItem('pongProgress'));
if (savedData) {
    game.level = savedData.level;
    game.lives = savedData.lives;
    document.getElementById('nivel').textContent = game.level;
    document.getElementById('vidas').textContent = game.lives;
}

// 🏓 Paletas y bola
const paddle = { width: 12, height: 80, speed: 7 };
const player = { x: 10, y: canvas.height / 2 - 40, width: paddle.width, height: paddle.height };
const ai = { x: canvas.width - 22, y: canvas.height / 2 - 40, width: paddle.width, height: paddle.height };
let ball = { x: canvas.width/2, y: canvas.height/2, r: 8, speedX: 4, speedY: 3, baseSpeed: 4 };
let keys = {};

// 🎮 Controles
document.addEventListener('keydown', e => {
    keys[e.key.toLowerCase()] = true;
    if (e.key === ' ') togglePause();
});
document.addEventListener('keyup', e => { keys[e.key.toLowerCase()] = false; });

// 🔄 Botón reinicio
document.getElementById('reiniciarBtn').addEventListener('click', () => {
    reiniciarJuego();
});

function togglePause() {
    if (!game.running) {
        game.running = true;
        loop();
    } else {
        game.paused = !game.paused;
    }
}

// 🔁 Reiniciar bola
function resetBall(direction = 1) {
    ball.x = canvas.width / 2;
    ball.y = canvas.height / 2;
    const dir = direction === 1 ? 1 : -1;
    ball.speedX = dir * ball.baseSpeed;
    ball.speedY = (Math.random() - 0.5) * ball.baseSpeed;
}

// 💾 Guardar progreso
function guardarProgreso() {
    localStorage.setItem('pongProgress', JSON.stringify({ level: game.level, lives: game.lives }));
}

// 🔄 Reiniciar todo el juego
function reiniciarJuego() {
    game.score = 0;
    game.lives = 5;
    game.level = 1;
    game.gameOver = false;
    document.getElementById('score').textContent = game.score;
    document.getElementById('vidas').textContent = game.lives;
    document.getElementById('nivel').textContent = game.level;
    document.getElementById('statusText').textContent = "▶ Presiona ESPACIO para comenzar";
    resetBall();
    guardarProgreso();
}

// ⚙️ Actualizar juego
function update() {
    if (game.paused || game.gameOver) return;

    // Movimiento jugador
    if (keys['w'] || keys['arrowup']) player.y -= paddle.speed;
    if (keys['s'] || keys['arrowdown']) player.y += paddle.speed;

    // IA fácil/moderada
    const aiCenter = ai.y + ai.height / 2;
    const aiSpeed = 2 + game.level * 0.8;
    const delay = 20 - game.level * 2;

    if (ball.x > canvas.width / 3) {
        if (ball.y < aiCenter - delay) ai.y -= aiSpeed;
        if (ball.y > aiCenter + delay) ai.y += aiSpeed;
    }

    // límites
    player.y = Math.max(0, Math.min(canvas.height - player.height, player.y));
    ai.y = Math.max(0, Math.min(canvas.height - ai.height, ai.y));

    // movimiento bola y aceleración constante
    ball.x += ball.speedX;
    ball.y += ball.speedY;
    const factor = 1 + 0.002;
    ball.speedX *= factor;
    ball.speedY *= factor;

    // rebotes
    if (ball.y - ball.r < 0 || ball.y + ball.r > canvas.height) ball.speedY *= -1;

    // colisiones
    if (ball.x - ball.r < player.x + player.width && ball.y > player.y && ball.y < player.y + player.height) {
        ball.speedX = Math.abs(ball.speedX);
        ball.speedY += (Math.random() - 0.5);
    }
    if (ball.x + ball.r > ai.x && ball.y > ai.y && ball.y < ai.y + ai.height) {
        ball.speedX = -Math.abs(ball.speedX);
        ball.speedY += (Math.random() - 0.5);
    }

    // punto jugador
    if (ball.x + ball.r > canvas.width) {
        game.score++;
        document.getElementById('score').textContent = game.score;
        if (game.score % 3 === 0 && game.level < game.maxLevel) game.level++;
        document.getElementById('nivel').textContent = game.level;
        guardarProgreso();
        resetBall(-1);
        // 🏆 Check win
        if (game.score >= game.winScore) {
            game.gameOver = true;
            document.getElementById('statusText').textContent = "🎉 YOU WIN!";
        }
    }

    // punto IA
    if (ball.x - ball.r < 0) {
        game.lives--;
        document.getElementById('vidas').textContent = game.lives;
        guardarProgreso();
        resetBall(1);
        if (game.lives <= 0) {
            game.gameOver = true;
            document.getElementById('statusText').textContent = "💀 GAME OVER";
        }
    }
}

// 🎨 Dibujar todo
function draw() {
    ctx.fillStyle = '#1e293b';
    ctx.fillRect(0, 0, canvas.width, canvas.height);

    // línea central
    ctx.strokeStyle = '#475569';
    ctx.setLineDash([5,5]);
    ctx.beginPath();
    ctx.moveTo(canvas.width/2,0);
    ctx.lineTo(canvas.width/2,canvas.height);
    ctx.stroke();
    ctx.setLineDash([]);

    // paletas
    ctx.fillStyle = '#6366f1';
    ctx.fillRect(player.x, player.y, player.width, player.height);
    ctx.fillRect(ai.x, ai.y, ai.width, ai.height);

    // bola
    ctx.fillStyle = '#ec4899';
    ctx.beginPath();
    ctx.arc(ball.x, ball.y, ball.r, 0, Math.PI*2);
    ctx.fill();
}

// 🧠 Bucle principal
function loop() {
    if (game.running) {
        update();
        draw();
        requestAnimationFrame(loop);
    }
}

draw();
</script>





</body>
</html>
