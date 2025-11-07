<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php'); // o donde esté el login
    exit;
}
$usuario =$_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Juego de Disparos Espacial</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: #000;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            font-family: Arial, sans-serif;
            overflow: hidden;
        }
        #gameContainer {
            position: relative;
            width: 800px;
            height: 600px;
            background: linear-gradient(to bottom, #000033, #000011);
            border: 3px solid #00ffff;
            overflow: hidden;
        }
        #canvas {
            width: 100%;
            height: 100%;
        }
        #menu, #gameOver {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
            color: #fff;
            background: rgba(0, 0, 0, 0.8);
            padding: 40px;
            border-radius: 10px;
            border: 2px solid #00ffff;
        }
        h1 {
            color: #00ffff;
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 0 0 10px #00ffff;
        }
        button {
            background: #00ffff;
            color: #000;
            border: none;
            padding: 15px 40px;
            font-size: 24px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            margin: 10px;
            transition: all 0.3s;
        }
        button:hover {
            background: #00cccc;
            transform: scale(1.1);
        }
        #hud {
            position: absolute;
            top: 10px;
            left: 10px;
            color: #fff;
            font-size: 20px;
            text-shadow: 2px 2px 4px #000;
        }
        .stat {
            margin: 5px 0;
            color: #00ffff;
        }
        #menuButton {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(0, 255, 255, 0.8);
            color: #000;
            border: 2px solid #00ffff;
            padding: 10px 20px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: all 0.3s;
        }
        #menuButton:hover {
            background: #00ffff;
            transform: scale(1.05);
        }
        .instructions {
            color: #aaa;
            margin-top: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div id="gameContainer">
        <canvas id="canvas"></canvas>
        
        <div id="hud">
            <div class="stat">Puntuación: <span id="score">0</span></div>
            <div class="stat">Vidas: <span id="lives">3</span></div>
            <div class="stat">Oleada: <span id="wave">1</span></div>
        </div>

        <button id="menuButton" onclick="location.href='../../plataforma.php'" style="display:none;">🏠 VOLVER</button>

        <div id="menu">
            <h1>🚀 SPACE SHOOTER 🚀</h1>
            <button onclick="startGame()">JUGAR</button>
            <div class="instructions">
                <p>⬅️ ➡️ Mover nave</p>
                <p>ESPACIO - Disparar</p>
                <p>¡Destruye todos los enemigos!</p>
            </div>
        </div>

        <div id="gameOver" style="display:none;">
            <h1>GAME OVER</h1>
            <p style="font-size: 24px; margin: 20px 0;">Puntuación Final: <span id="finalScore">0</span></p>
            <button onclick="startGame()">JUGAR DE NUEVO</button>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 800;
        canvas.height = 600;

        let gameRunning = false;
        let player = { x: 375, y: 520, width: 50, height: 50, speed: 8 };
        let bullets = [];
        let enemies = [];
        let explosions = [];
        let stars = [];
        let score = 0;
        let lives = 3;
        let wave = 1;
        let keys = {};
        let enemySpawnTimer = 0;
        let enemySpawnDelay = 120; // Más tiempo al inicio
        let shootCooldown = 0;
        let shootDelay = 15; // Cooldown entre disparos

        // Inicializar estrellas de fondo
        for (let i = 0; i < 100; i++) {
            stars.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                size: Math.random() * 2
            });
        }

        document.addEventListener('keydown', (e) => {
            keys[e.key] = true;
            if (e.key === ' ' && gameRunning && shootCooldown <= 0) {
                e.preventDefault();
                shoot();
                shootCooldown = shootDelay;
            }
        });

        document.addEventListener('keyup', (e) => {
            keys[e.key] = false;
        });

        function startGame() {
            document.getElementById('menu').style.display = 'none';
            document.getElementById('gameOver').style.display = 'none';
            document.getElementById('menuButton').style.display = 'block';
            gameRunning = true;
            player.x = 375;
            player.y = 520;
            bullets = [];
            enemies = [];
            explosions = [];
            score = 0;
            lives = 3;
            wave = 1;
            enemySpawnDelay = 120; // Reiniciar dificultad
            shootCooldown = 0;
            updateHUD();
            gameLoop();
        }

        function shoot() {
            bullets.push({
                x: player.x + player.width / 2 - 2,
                y: player.y,
                width: 4,
                height: 15,
                speed: 10
            });
        }

        function spawnEnemy() {
            const size = 30 + Math.random() * 20;
            const baseSpeed = 0.5 + (wave - 1) * 0.2; // Aumenta con oleadas
            enemies.push({
                x: Math.random() * (canvas.width - size),
                y: -size,
                width: size,
                height: size,
                speed: baseSpeed + Math.random() * 1.5,
                type: Math.floor(Math.random() * 3)
            });
        }

        function createExplosion(x, y) {
            for (let i = 0; i < 15; i++) {
                explosions.push({
                    x: x,
                    y: y,
                    vx: (Math.random() - 0.5) * 8,
                    vy: (Math.random() - 0.5) * 8,
                    life: 30
                });
            }
        }

        function updateHUD() {
            document.getElementById('score').textContent = score;
            document.getElementById('lives').textContent = lives;
            document.getElementById('wave').textContent = wave;
        }

        function checkCollision(rect1, rect2) {
            return rect1.x < rect2.x + rect2.width &&
                   rect1.x + rect1.width > rect2.x &&
                   rect1.y < rect2.y + rect2.height &&
                   rect1.y + rect1.height > rect2.y;
        }

        function gameLoop() {
            if (!gameRunning) return;

            // Reducir cooldown de disparo
            if (shootCooldown > 0) shootCooldown--;

            ctx.fillStyle = '#000011';
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Dibujar estrellas
            stars.forEach(star => {
                ctx.fillStyle = '#fff';
                ctx.fillRect(star.x, star.y, star.size, star.size);
                star.y += 1;
                if (star.y > canvas.height) {
                    star.y = 0;
                    star.x = Math.random() * canvas.width;
                }
            });

            // Mover jugador
            if (keys['ArrowLeft'] && player.x > 0) {
                player.x -= player.speed;
            }
            if (keys['ArrowRight'] && player.x < canvas.width - player.width) {
                player.x += player.speed;
            }

            // Dibujar jugador
            ctx.fillStyle = '#00ffff';
            ctx.beginPath();
            ctx.moveTo(player.x + player.width / 2, player.y);
            ctx.lineTo(player.x, player.y + player.height);
            ctx.lineTo(player.x + player.width, player.y + player.height);
            ctx.closePath();
            ctx.fill();

            // Actualizar y dibujar balas
            bullets = bullets.filter(bullet => {
                bullet.y -= bullet.speed;
                ctx.fillStyle = '#ffff00';
                ctx.fillRect(bullet.x, bullet.y, bullet.width, bullet.height);
                return bullet.y > 0;
            });

            // Generar enemigos
            enemySpawnTimer++;
            if (enemySpawnTimer > enemySpawnDelay) {
                spawnEnemy();
                enemySpawnTimer = 0;
                // Reducir delay progresivamente (más difícil)
                if (enemySpawnDelay > 30) {
                    enemySpawnDelay -= 0.3;
                }
            }

            // Actualizar y dibujar enemigos
            enemies = enemies.filter(enemy => {
                enemy.y += enemy.speed;

                // Dibujar enemigo según tipo
                if (enemy.type === 0) {
                    ctx.fillStyle = '#ff0000';
                    ctx.fillRect(enemy.x, enemy.y, enemy.width, enemy.height);
                } else if (enemy.type === 1) {
                    ctx.fillStyle = '#ff00ff';
                    ctx.beginPath();
                    ctx.arc(enemy.x + enemy.width/2, enemy.y + enemy.height/2, enemy.width/2, 0, Math.PI * 2);
                    ctx.fill();
                } else {
                    ctx.fillStyle = '#00ff00';
                    ctx.beginPath();
                    ctx.moveTo(enemy.x + enemy.width/2, enemy.y);
                    ctx.lineTo(enemy.x, enemy.y + enemy.height);
                    ctx.lineTo(enemy.x + enemy.width, enemy.y + enemy.height);
                    ctx.closePath();
                    ctx.fill();
                }

                // Colisión con jugador
                if (checkCollision(player, enemy)) {
                    lives--;
                    updateHUD();
                    createExplosion(enemy.x + enemy.width/2, enemy.y + enemy.height/2);
                    if (lives <= 0) {
                        gameOver();
                    }
                    return false;
                }

                return enemy.y < canvas.height;
            });

            // Colisiones bala-enemigo
            bullets.forEach(bullet => {
                enemies.forEach((enemy, index) => {
                    if (checkCollision(bullet, enemy)) {
                        score += 10 * wave;
                        updateHUD();
                        createExplosion(enemy.x + enemy.width/2, enemy.y + enemy.height/2);
                        enemies.splice(index, 1);
                        bullets.splice(bullets.indexOf(bullet), 1);
                        
                        // Cambiar de oleada cada 150 puntos
                        if (score > 0 && score % 150 === 0) {
                            wave++;
                            updateHUD();
                            // Resetear dificultad de spawn para la nueva oleada
                            enemySpawnDelay = Math.max(30, 120 - (wave - 1) * 15);
                        }
                    }
                });
            });

            // Actualizar y dibujar explosiones
            explosions = explosions.filter(particle => {
                particle.x += particle.vx;
                particle.y += particle.vy;
                particle.life--;
                ctx.fillStyle = `rgba(255, ${255 - particle.life * 8}, 0, ${particle.life / 30})`;
                ctx.fillRect(particle.x, particle.y, 3, 3);
                return particle.life > 0;
            });

            requestAnimationFrame(gameLoop);
        }

        function gameOver() {
            gameRunning = false;
            document.getElementById('menuButton').style.display = 'none';
            document.getElementById('finalScore').textContent = score;
            document.getElementById('gameOver').style.display = 'block';
        }
    </script>
</body>
</html>