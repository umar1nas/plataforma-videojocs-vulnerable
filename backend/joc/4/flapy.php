<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Verificar sesión del usuario
if (!isset($_SESSION['usuari_id'])) {
    die('Error: No hay sesión activa. Por favor inicia sesión primero.');
}

$usuari_id = $_SESSION['usuari_id'];
$joc_id = 4; // ID del juego Flappy Bird

// Obtener nivel actual del usuario desde la base de datos
require_once "../../include/db_mysqli.php";

// Verificar si hay progreso del usuario
$sql_progres = "SELECT nivell_actual FROM progres_usuari WHERE usuari_id = $usuari_id AND joc_id = $joc_id LIMIT 1";
$res_progres = $conn->query($sql_progres);

$nivell_actual = 1;
if ($res_progres && $res_progres->num_rows > 0) {
    $row = $res_progres->fetch_assoc();
    $nivell_actual = (int)$row['nivell_actual'];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🐦 Flappy Bird - Plataforma de Videojocs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            width: 100%;
            max-width: 700px;
        }

        .header {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            padding: 25px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }

        .header h1 {
            font-size: 32px;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
        }

        .game-wrapper {
            background-color: #1e293b;
            padding: 25px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .score-board {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 20px;
        }

        .score-item {
            background-color: #334155;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #475569;
            text-align: center;
        }

        .score-label {
            font-size: 11px;
            color: #94a3b8;
            margin-bottom: 5px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .score-value {
            font-size: 28px;
            font-weight: bold;
            color: #6366f1;
        }

        .score-value.level {
            color: #10b981;
        }

        .score-value.time {
            color: #f59e0b;
        }

        .game-container {
            position: relative;
            margin-bottom: 20px;
        }

        canvas {
            display: block;
            width: 100%;
            border: 3px solid #6366f1;
            border-radius: 10px;
            cursor: pointer;
            background: linear-gradient(180deg, #87CEEB 0%, #E0F6FF 70%, #DEB887 70%, #DEB887 100%);
        }

        .game-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(15, 23, 42, 0.97);
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            border: 3px solid #6366f1;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.7);
            display: none;
            min-width: 300px;
        }

        .game-overlay.active {
            display: block;
        }

        .overlay-title {
            font-size: 36px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .overlay-subtitle {
            font-size: 18px;
            color: #94a3b8;
            margin-bottom: 20px;
        }

        .overlay-info {
            background-color: #334155;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #6366f1;
        }

        .overlay-info p {
            font-size: 14px;
            color: #cbd5e1;
            margin: 5px 0;
        }

        .overlay-score {
            font-size: 20px;
            margin-bottom: 10px;
            color: #cbd5e1;
        }

        .overlay-score strong {
            color: #fbbf24;
            font-size: 24px;
        }

        .overlay-instruction {
            font-size: 13px;
            color: #94a3b8;
            margin: 15px 0;
        }

        .buttons-container {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        button, .btn-link {
            padding: 12px 25px;
            font-size: 15px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            color: white;
            box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(99, 102, 241, 0.5);
        }

        .btn-secondary {
            background-color: #475569;
            color: #f1f5f9;
            border: 2px solid #6366f1;
        }

        .btn-secondary:hover {
            background-color: #334155;
            border-color: #ec4899;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
        }

        .instructions {
            background-color: #334155;
            padding: 15px;
            border-radius: 10px;
            border-left: 4px solid #6366f1;
            font-size: 13px;
            color: #cbd5e1;
        }

        .instructions h4 {
            color: #6366f1;
            margin-bottom: 10px;
            font-size: 15px;
        }

        .instructions ul {
            margin-left: 20px;
            line-height: 1.6;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 24px;
            }

            .score-value {
                font-size: 22px;
            }

            .overlay-title {
                font-size: 28px;
            }

            .game-overlay {
                padding: 20px;
                min-width: 250px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🐦 Flappy Bird</h1>
            <p>Prem ESPAI o fes clic per volar. Evita les tuberíes!</p>
        </div>

        <div class="game-wrapper">
            <div class="score-board">
                <div class="score-item">
                    <div class="score-label">Puntuació</div>
                    <div class="score-value" id="currentScore">0</div>
                </div>
                <div class="score-item">
                    <div class="score-label">Nivell</div>
                    <div class="score-value level" id="currentLevel"><?php echo $nivell_actual; ?></div>
                </div>
                <div class="score-item">
                    <div class="score-label">⏱ Temps</div>
                    <div class="score-value time" id="currentTime">0s</div>
                </div>
            </div>

            <div class="game-container">
                <canvas id="gameCanvas" width="650" height="500"></canvas>
                
                <div class="game-overlay active" id="startOverlay">
                    <div class="overlay-title">🐦 Flappy Bird</div>
                    <div class="overlay-subtitle" id="levelInfo">Carregant nivell...</div>
                    <div class="overlay-info">
                        <p><strong>Objectiu:</strong> <span id="targetObstacles">0</span> obstacles</p>
                        <p><strong>Puntuació mínima:</strong> <span id="minScoreDisplay">0</span> punts</p>
                    </div>
                    <div class="overlay-instruction">Prem ESPAI o fes clic per començar</div>
                    <button class="btn-primary" onclick="startGame()">▶ Jugar</button>
                </div>

                <div class="game-overlay" id="gameOverOverlay">
                    <div class="overlay-title">💀 Game Over</div>
                    <div class="overlay-score">Puntuació: <strong id="finalScore">0</strong></div>
                    <div class="overlay-score">Necessitaves: <strong id="requiredScore">0</strong> punts</div>
                    <div class="overlay-instruction">No has aconseguit superar el nivell</div>
                    <button class="btn-primary" onclick="restartGame()">🔄 Tornar a intentar</button>
                </div>

                <div class="game-overlay" id="levelCompleteOverlay">
                    <div class="overlay-title">🎉 Nivell Completat!</div>
                    <div class="overlay-score">Puntuació: <strong id="completeScore">0</strong></div>
                    <div class="overlay-instruction" id="nextLevelText">Has superat el nivell!</div>
                    <button class="btn-success" id="nextLevelBtn" onclick="nextLevel()">➡ Següent Nivell</button>
                </div>
            </div>

            <div class="buttons-container">
                <a href="../../index.php" class="btn-secondary">← Tornar</a>
            </div>

            <div class="instructions">
                <h4>📋 Instruccions</h4>
                <ul>
                    <li>Fes clic o prem ESPAI per fer volar l'ocell</li>
                    <li>Evita les tuberíes verdes</li>
                    <li>Passa tots els obstacles per completar el nivell</li>
                    <li>Aconsegueix la puntuació mínima requerida</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        // Configuración del juego desde PHP
        const USUARIO_ID = <?php echo $usuari_id; ?>;
        const JOC_ID = <?php echo $joc_id; ?>;
        let currentLevel = <?php echo $nivell_actual; ?>;
        
        // Objeto para almacenar la configuración de los niveles
        let nivelesConfig = {};
        
        // Cargar configuración desde la API para todos los niveles (1-5)
        async function cargarConfiguracionAPI() {
            console.log("Cargando configuración desde la API...");
            
            for (let i = 1; i <= 5; i++) {
                try {
                    const response = await fetch(`http://192.168.1.148/projecte/backend/api.php?jocs=${JOC_ID}&nivells=${i}`);
                    const data = await response.json();
                    
                    console.log(`Respuesta API Nivel ${i}:`, data);
                    
                    nivelesConfig[i] = {
                        velocidad: data.velocidad || 5,
                        obstaculos: data.obstaculos || 10,
                        puntuacio_minima: data.puntuacio_minima || (i * 10),
                        nom: `Nivel ${i}`
                    };
                    
                } catch (error) {
                    console.error(`Error cargando nivel ${i} desde API:`, error);
                    nivelesConfig[i] = {
                        velocidad: 4 + i,
                        obstaculos: 5 + (i * 5),
                        puntuacio_minima: i * 10,
                        nom: `Nivel ${i}`
                    };
                }
            }
            
            console.log("Configuración de niveles cargada:", nivelesConfig);
            loadLevelConfig();
        }
        
        cargarConfiguracionAPI();
        
        // Canvas y contexto
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // Variables del juego
        let bird = {
            x: 100,
            y: 250,
            width: 30,
            height: 30,
            velocity: 0,
            gravity: 0.5,
            jumpPower: -8,
            rotation: 0
        };
        
        let pipes = [];
        let score = 0;
        let gameStarted = false;
        let gameOver = false;
        let startTime = null;
        let elapsedTime = 0;
        let timerInterval = null;
        
        let pipeGap = 150;
        let pipeWidth = 60;
        let pipeSpeed = 2;
        let pipeSpacing = 220;
        let pipesPerLevel = 10;
        let pipesPassed = 0;
        
        // Cargar configuración del nivel actual
        function loadLevelConfig() {
            const config = nivelesConfig[currentLevel];
            if (config) {
                pipeSpeed = config.velocidad;
                pipesPerLevel = config.obstaculos;
                
                document.getElementById('levelInfo').textContent = config.nom;
                document.getElementById('targetObstacles').textContent = config.obstaculos;
                document.getElementById('minScoreDisplay').textContent = config.puntuacio_minima;
                document.getElementById('currentLevel').textContent = currentLevel;
            }
        }
        
        // Event listeners
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                if (!gameStarted) {
                    startGame();
                } else if (!gameOver) {
                    bird.velocity = bird.jumpPower;
                }
            }
        });
        
        canvas.addEventListener('click', () => {
            if (!gameStarted) {
                startGame();
            } else if (!gameOver) {
                bird.velocity = bird.jumpPower;
            }
        });
        
        function startGame() {
            document.getElementById('startOverlay').classList.remove('active');
            gameStarted = true;
            gameOver = false;
            bird.y = 250;
            bird.velocity = 0;
            pipes = [];
            score = 0;
            pipesPassed = 0;
            elapsedTime = 0;
            startTime = Date.now();
            
            loadLevelConfig();
            
            document.getElementById('currentScore').textContent = '0';
            document.getElementById('currentTime').textContent = '0s';
            
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(updateTimer, 1000);
            
            gameLoop();
        }
        
        function updateTimer() {
            if (gameStarted && !gameOver) {
                elapsedTime = Math.floor((Date.now() - startTime) / 1000);
                document.getElementById('currentTime').textContent = elapsedTime + 's';
            }
        }
        
        function restartGame() {
            document.getElementById('gameOverOverlay').classList.remove('active');
            startGame();
        }
        
        function nextLevel() {
            document.getElementById('levelCompleteOverlay').classList.remove('active');
            currentLevel++;
            if (currentLevel > 5) {
                alert('Felicitats! Has completat tots els nivells!');
                currentLevel = 1;
            }
            document.getElementById('currentLevel').textContent = currentLevel;
            startGame();
        }
        
        function createPipe() {
            const minHeight = 50;
            const maxHeight = canvas.height - pipeGap - minHeight - 50;
            const height = Math.floor(Math.random() * (maxHeight - minHeight + 1)) + minHeight;
            
            pipes.push({
                x: canvas.width,
                top: height,
                bottom: height + pipeGap,
                passed: false
            });
        }
        
        function updateBird() {
            bird.velocity += bird.gravity;
            bird.y += bird.velocity;
            bird.rotation = Math.min(Math.max(bird.velocity * 3, -25), 90);

            if (bird.y + bird.height > canvas.height - 30 || bird.y < 0) {
                endGame();
            }
        }
        
        function updatePipes() {
            if (pipes.length === 0 || pipes[pipes.length - 1].x < canvas.width - pipeSpacing) {
                if (pipesPassed < pipesPerLevel) {
                    createPipe();
                }
            }

            for (let i = pipes.length - 1; i >= 0; i--) {
                pipes[i].x -= pipeSpeed;

                if (!pipes[i].passed && pipes[i].x + pipeWidth < bird.x) {
                    pipes[i].passed = true;
                    score++;
                    pipesPassed++;
                    document.getElementById('currentScore').textContent = score;
                }

                if (pipes[i].x + pipeWidth < 0) {
                    pipes.splice(i, 1);
                }

                if (checkCollision(pipes[i])) {
                    endGame();
                }
            }
        }
        
        function checkCollision(pipe) {
            if (
                bird.x + bird.width > pipe.x &&
                bird.x < pipe.x + pipeWidth &&
                bird.y < pipe.top
            ) {
                return true;
            }

            if (
                bird.x + bird.width > pipe.x &&
                bird.x < pipe.x + pipeWidth &&
                bird.y + bird.height > pipe.bottom
            ) {
                return true;
            }

            return false;
        }
        
        function endGame() {
            gameOver = true;
            gameStarted = false;
            clearInterval(timerInterval);
            
            savePartida(currentLevel, score, elapsedTime);
            
            const config = nivelesConfig[currentLevel];
            const minScore = config ? config.puntuacio_minima : 0;
            
            if (pipesPassed >= pipesPerLevel && score >= minScore) {
                document.getElementById('completeScore').textContent = score;
                
                if (currentLevel < 5) {
                    document.getElementById('nextLevelText').textContent = 'Has superat el nivell! Pots continuar.';
                } else {
                    document.getElementById('nextLevelText').textContent = 'Has completat tots els nivells!';
                    document.getElementById('nextLevelBtn').textContent = '🔄 Tornar al Nivel 1';
                }
                
                document.getElementById('levelCompleteOverlay').classList.add('active');
            } else {
                document.getElementById('finalScore').textContent = score;
                document.getElementById('requiredScore').textContent = minScore;
                document.getElementById('gameOverOverlay').classList.add('active');
            }
        }
        
        function savePartida(nivel, puntuacion, duracion) {
            const url = `/projecte/backend/save_partida.php?usuari_id=${USUARIO_ID}&joc_id=${JOC_ID}&nivell=${nivel}&puntuacio=${puntuacion}&durada=${duracion}`;
            
            console.log('Guardando partida:', url);
            
            fetch(url)
                .then(response => response.text())
                .then(text => {
                    try {
                        const data = JSON.parse(text);
                        console.log('Partida guardada:', data);
                    } catch (e) {
                        console.error('Error al parsear respuesta:', text);
                    }
                })
                .catch(error => {
                    console.error('Error al guardar partida:', error);
                });
        }
        
        function draw() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            drawClouds();
            drawPipes();
            drawBird();
            drawGround();
        }
        
        function drawClouds() {
            ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
            
            ctx.beginPath();
            ctx.arc(100, 80, 20, 0, Math.PI * 2);
            ctx.arc(120, 70, 25, 0, Math.PI * 2);
            ctx.arc(140, 80, 20, 0, Math.PI * 2);
            ctx.fill();

            ctx.beginPath();
            ctx.arc(canvas.width - 120, 120, 25, 0, Math.PI * 2);
            ctx.arc(canvas.width - 95, 110, 30, 0, Math.PI * 2);
            ctx.arc(canvas.width - 70, 120, 25, 0, Math.PI * 2);
            ctx.fill();
        }
        
        function drawBird() {
            ctx.save();
            ctx.translate(bird.x + bird.width / 2, bird.y + bird.height / 2);
            ctx.rotate((bird.rotation * Math.PI) / 180);
            
            ctx.fillStyle = '#FFEB3B';
            ctx.beginPath();
            ctx.ellipse(0, 0, bird.width / 2, bird.height / 2, 0, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.fillStyle = 'white';
            ctx.beginPath();
            ctx.arc(bird.width / 4, -bird.height / 6, 5, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.fillStyle = 'black';
            ctx.beginPath();
            ctx.arc(bird.width / 4, -bird.height / 6, 3, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.fillStyle = '#FF5722';
            ctx.beginPath();
            ctx.moveTo(bird.width / 2, 0);
            ctx.lineTo(bird.width / 2 + 10, -3);
            ctx.lineTo(bird.width / 2 + 10, 3);
            ctx.closePath();
            ctx.fill();
            
            ctx.restore();
        }
        
        function drawPipes() {
            pipes.forEach(pipe => {
                ctx.fillStyle = '#4CAF50';
                ctx.strokeStyle = '#388E3C';
                ctx.lineWidth = 3;
                
                ctx.fillRect(pipe.x, 0, pipeWidth, pipe.top);
                ctx.strokeRect(pipe.x, 0, pipeWidth, pipe.top);
                
                ctx.fillRect(pipe.x - 5, pipe.top - 20, pipeWidth + 10, 20);
                ctx.strokeRect(pipe.x - 5, pipe.top - 20, pipeWidth + 10, 20);
                
                ctx.fillRect(pipe.x, pipe.bottom, pipeWidth, canvas.height - pipe.bottom - 30);
                ctx.strokeRect(pipe.x, pipe.bottom, pipeWidth, canvas.height - pipe.bottom - 30);
                
                ctx.fillRect(pipe.x - 5, pipe.bottom, pipeWidth + 10, 20);
                ctx.strokeRect(pipe.x - 5, pipe.bottom, pipeWidth + 10, 20);
            });
        }
        
        function drawGround() {
            ctx.fillStyle = '#C8A959';
            ctx.fillRect(0, canvas.height - 30, canvas.width, 30);
            ctx.strokeStyle = '#8B7355';
            ctx.lineWidth = 2;
            ctx.strokeRect(0, canvas.height - 30, canvas.width, 30);
        }
        
        function gameLoop() {
            if (!gameStarted || gameOver) return;
            
            updateBird();
            updatePipes();
            draw();
            
            if (pipesPassed >= pipesPerLevel && pipes.length === 0) {
                endGame();
                return;
            }
            
            requestAnimationFrame(gameLoop);
        }
        
        draw();
    </script>
</body>
</html>