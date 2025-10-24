<?php
session_start();
if (!isset($_SESSION['nivell'])) {
    $_SESSION['nivell'] = 1; 
}

// Información del juego
$juego_info = [
    'nombre' => 'Flappy Bird',
    'descripcion' => 'Haz clic o presiona ESPACIO para volar. Evita las tuberías y consigue la mayor puntuación posible'
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🐦 Flappy Bird - Game Portal</title>
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
            max-width: 900px;
        }

        .header {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            padding: 30px;
            border-radius: 15px 15px 0 0;
            text-align: center;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }

        .header h1 {
            font-size: 36px;
            margin-bottom: 10px;
        }

        .header p {
            font-size: 14px;
            color: rgba(255, 255, 255, 0.9);
        }

        .game-wrapper {
            background-color: #1e293b;
            padding: 30px;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .score-board {
            display: flex;
            justify-content: space-around;
            align-items: center;
            background-color: #334155;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #475569;
            margin-bottom: 20px;
        }

        .score-item {
            text-align: center;
        }

        .score-label {
            font-size: 12px;
            color: #94a3b8;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .score-value {
            font-size: 36px;
            font-weight: bold;
            color: #6366f1;
        }

        .score-value.record {
            color: #fbbf24;
        }

        .game-container {
            position: relative;
            margin-bottom: 20px;
        }

        canvas {
            display: block;
            width: 100%;
            background: linear-gradient(180deg, #87CEEB 0%, #E0F6FF 100%);
            border: 3px solid #6366f1;
            border-radius: 10px;
            cursor: pointer;
        }

        .game-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: rgba(15, 23, 42, 0.95);
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            border: 3px solid #6366f1;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            display: none;
        }

        .game-overlay.active {
            display: block;
        }

        .overlay-title {
            font-size: 32px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .overlay-score {
            font-size: 24px;
            margin-bottom: 10px;
            color: #cbd5e1;
        }

        .overlay-record {
            font-size: 18px;
            margin-bottom: 30px;
            color: #fbbf24;
        }

        .overlay-instruction {
            font-size: 14px;
            color: #94a3b8;
            margin-bottom: 30px;
        }

        .level-info {
            font-size: 16px;
            color: #6366f1;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(15, 23, 42, 0.95);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .loading-content {
            text-align: center;
            color: #f1f5f9;
        }

        .loading-spinner {
            border: 4px solid #334155;
            border-top: 4px solid #6366f1;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .buttons-container {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }

        button, .btn-link {
            padding: 12px 30px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
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

        .btn-back {
            background-color: #334155;
            color: #cbd5e1;
            border: 2px solid #475569;
            padding: 10px 20px;
            font-size: 14px;
        }

        .btn-back:hover {
            background-color: #475569;
            border-color: #6366f1;
        }

        .instructions {
            background-color: #334155;
            padding: 20px;
            border-radius: 10px;
            border-left: 4px solid #6366f1;
            margin-top: 20px;
            font-size: 14px;
            color: #cbd5e1;
        }

        .instructions h4 {
            color: #6366f1;
            margin-bottom: 10px;
            font-size: 16px;
        }

        .instructions ul {
            margin-left: 20px;
            line-height: 1.8;
        }

        .level-selector {
            background-color: #334155;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #475569;
            margin-bottom: 20px;
            text-align: center;
        }

        .level-selector label {
            margin-right: 15px;
            font-size: 14px;
            color: #cbd5e1;
        }

        .level-selector select {
            padding: 8px 15px;
            border-radius: 5px;
            background-color: #1e293b;
            color: #f1f5f9;
            border: 2px solid #6366f1;
            font-size: 14px;
            cursor: pointer;
        }

        .error-message {
            background-color: #7f1d1d;
            color: #fecaca;
            padding: 15px;
            border-radius: 10px;
            border: 2px solid #dc2626;
            margin-bottom: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .header h1 {
                font-size: 28px;
            }

            .score-value {
                font-size: 28px;
            }

            .overlay-title {
                font-size: 24px;
            }

            .game-overlay {
                padding: 30px 20px;
            }

            canvas {
                border-width: 2px;
            }
        }
    </style>
</head>
<body>
    <div id="loadingOverlay" class="loading-overlay">
        <div class="loading-content">
            <div class="loading-spinner"></div>
            <p>Cargando configuración del juego...</p>
        </div>
    </div>

    <div class="container">
        <div class="header">
            <h1>🐦 Flappy Bird</h1>
            <p><?php echo htmlspecialchars($juego_info['descripcion']); ?></p>
        </div>

        <div class="game-wrapper">
            <div id="errorContainer"></div>

            <div class="score-board">
                <div class="score-item">
                    <div class="score-label">Nivel</div>
                    <div class="score-value" id="currentLevel">1</div>
                </div>
                <div class="score-item">
                    <div class="score-label">Puntuación</div>
                    <div class="score-value" id="currentScore">0</div>
                </div>
                <div class="score-item">
                    <div class="score-label">🏆 Récord</div>
                    <div class="score-value record" id="highScore">0</div>
                </div>
            </div>

            <div class="level-selector">
                <label for="levelSelect">🎯 Selecciona Nivel:</label>
                <select id="levelSelect" onchange="changeLevel()">
                    <option value="1">Nivel 1</option>
                    <option value="2">Nivel 2</option>
                    <option value="3">Nivel 3</option>
                    <option value="4">Nivel 4</option>
                    <option value="5">Nivel 5</option>
                </select>
            </div>

            <div class="game-container">
                <canvas id="gameCanvas"></canvas>
                
                <div class="game-overlay active" id="startOverlay">
                    <div class="overlay-title">🐦 Flappy Bird</div>
                    <div class="level-info" id="levelInfoStart">Cargando nivel...</div>
                    <div class="overlay-instruction">Haz clic o presiona ESPACIO para comenzar</div>
                    <button class="btn-primary" onclick="startGame()">▶ Jugar</button>
                </div>

                <div class="game-overlay" id="gameOverOverlay">
                    <div class="overlay-title">💀 Game Over</div>
                    <div class="overlay-score">Puntuación: <span id="finalScore">0</span></div>
                    <div class="overlay-record">Récord: <span id="finalHighScore">0</span></div>
                    <div class="overlay-instruction">Haz clic o presiona ESPACIO para reintentar</div>
                    <div class="buttons-container" style="margin-top: 20px;">
                        <button class="btn-primary" onclick="restartGame()">🔄 Reintentar</button>
                        <button class="btn-secondary" onclick="nextLevel()">➡️ Siguiente Nivel</button>
                    </div>
                </div>
            </div>

            <div class="buttons-container">
                <button class="btn-secondary" onclick="togglePause()">⏸ Pausar</button>
                <a href="index.php" class="btn-back">← Volver</a>
            </div>

            <div class="instructions">
                <h4>📋 Instrucciones</h4>
                <ul>
                    <li>Haz clic en el canvas o presiona ESPACIO para hacer volar al pájaro.</li>
                    <li>Evita las tuberías verdes para seguir volando.</li>
                    <li>Cada tubería que pases suma 1 punto a tu puntuación.</li>
                    <li>El juego termina si tocas una tubería o el suelo.</li>
                    <li>Cada nivel aumenta la velocidad y cantidad de obstáculos.</li>
                    <li>¡Intenta superar tu récord!</li>
                </ul>
            </div>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // ID del juego Flappy Bird
        const jocId = 4;
        let nivell = <?php echo $_SESSION['nivell']; ?>;
        
        // Configuración cargada desde la API
        let apiConfig = null;
        
        // Configurar tamaño del canvas
        function resizeCanvas() {
            canvas.width = Math.min(600, window.innerWidth - 100);
            canvas.height = 500;
        }
        resizeCanvas();
        window.addEventListener('resize', resizeCanvas);

        // Variables del juego
        let game = {
            isRunning: false,
            isPaused: false,
            score: 0,
            highScore: parseInt(localStorage.getItem('flappyHighScore')) || 0,
            gravity: 0.5,
            pipeSpeed: 2,
            pipeGap: 150,
            maxObstacles: 10,
            currentLevel: nivell
        };

        // Pájaro
        const bird = {
            x: 100,
            y: canvas.height / 2,
            width: 30,
            height: 30,
            velocity: 0,
            jumpPower: -8,
            rotation: 0
        };

        // Tuberías
        let pipes = [];
        const pipeWidth = 60;
        const pipeSpacing = 220;

        // Colores
        const colors = {
            bird: '#FFEB3B',
            pipe: '#4CAF50',
            pipeOutline: '#388E3C',
            ground: '#C8A959'
        };

        // Función para cargar configuración desde la API
        async function loadLevelConfig(level) {
            try {
                // IMPORTANTE: Cambia esta IP por la IP de tu VM
                const response = await fetch(`http://172.18.33.249/projecte/backend/api.php?jocs=4&nivells=${level}`);
                
                if (!response.ok) {
                    throw new Error(`Error HTTP: ${response.status}`);
                }
                
                const data = await response.json();
                console.log("Resposta API:", data);
                
                if (data.error) {
                    throw new Error(data.error);
                }
                
                apiConfig = data;
                
                // Aplicar configuración del nivel
                game.pipeSpeed = data.velocidad || 5;
                game.maxObstacles = data.obstaculos || 10;
                game.currentLevel = level;
                
                // Ajustar dificultad según velocidad
                if (data.velocidad <= 5) {
                    game.gravity = 0.4;
                    game.pipeGap = 180;
                } else if (data.velocidad <= 7) {
                    game.gravity = 0.5;
                    game.pipeGap = 150;
                } else {
                    game.gravity = 0.6;
                    game.pipeGap = 130;
                }
                
                console.log(`Nivel: ${level}`);
                console.log(`Velocidad: ${game.pipeSpeed}`);
                console.log(`Obstáculos: ${game.maxObstacles}`);
                console.log(`Gap: ${game.pipeGap}`);
                
                // Actualizar UI
                document.getElementById('currentLevel').textContent = level;
                document.getElementById('levelInfoStart').textContent = 
                    `Nivel ${level} - Velocidad: ${game.pipeSpeed} - Obstáculos: ${game.maxObstacles}`;
                
                return true;
            } catch (error) {
                console.error("Error cargando configuración:", error);
                showError(`Error al cargar el nivel: ${error.message}`);
                return false;
            }
        }

        // Función para mostrar errores
        function showError(message) {
            const errorContainer = document.getElementById('errorContainer');
            errorContainer.innerHTML = `
                <div class="error-message">
                    <strong>⚠️ Error:</strong> ${message}
                </div>
            `;
        }

        // Función para cambiar de nivel
        async function changeLevel() {
            const selectedLevel = parseInt(document.getElementById('levelSelect').value);
            nivell = selectedLevel;
            
            // Mostrar loading
            document.getElementById('loadingOverlay').style.display = 'flex';
            
            const success = await loadLevelConfig(nivell);
            
            // Ocultar loading
            document.getElementById('loadingOverlay').style.display = 'none';
            
            if (success) {
                // Reiniciar el juego si está corriendo
                if (game.isRunning) {
                    restartGame();
                }
            }
        }

        // Función para pasar al siguiente nivel
        async function nextLevel() {
            if (nivell < 5) {
                nivell++;
                document.getElementById('levelSelect').value = nivell;
                await changeLevel();
                document.getElementById('gameOverOverlay').classList.remove('active');
                document.getElementById('startOverlay').classList.add('active');
            } else {
                alert('¡Felicidades! Has completado todos los niveles');
            }
        }

        // Controles
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                if (!game.isRunning) {
                    startGame();
                } else if (game.isRunning && !game.isPaused) {
                    jump();
                }
            }
        });

        canvas.addEventListener('click', () => {
            if (!game.isRunning) {
                startGame();
            } else if (game.isRunning && !game.isPaused) {
                jump();
            }
        });

        canvas.addEventListener('touchstart', (e) => {
            e.preventDefault();
            if (!game.isRunning) {
                startGame();
            } else if (game.isRunning && !game.isPaused) {
                jump();
            }
        });

        function startGame() {
            if (!apiConfig) {
                alert('Por favor, espera a que se cargue la configuración del nivel');
                return;
            }

            game.isRunning = true;
            game.isPaused = false;
            game.score = 0;
            bird.y = canvas.height / 2;
            bird.velocity = 0;
            pipes = [];
            createPipe();
            
            document.getElementById('startOverlay').classList.remove('active');
            document.getElementById('gameOverOverlay').classList.remove('active');
            document.getElementById('currentScore').textContent = '0';
            document.getElementById('highScore').textContent = game.highScore;
            
            gameLoop();
        }

        function restartGame() {
            startGame();
        }

        function togglePause() {
            if (game.isRunning) {
                game.isPaused = !game.isPaused;
                if (!game.isPaused) {
                    gameLoop();
                }
            }
        }

        function jump() {
            bird.velocity = bird.jumpPower;
        }

        function createPipe() {
            const minHeight = 50;
            const maxHeight = canvas.height - game.pipeGap - minHeight - 50;
            const height = Math.floor(Math.random() * (maxHeight - minHeight + 1)) + minHeight;
            
            pipes.push({
                x: canvas.width,
                top: height,
                bottom: height + game.pipeGap,
                passed: false
            });
        }

        function updateBird() {
            bird.velocity += game.gravity;
            bird.y += bird.velocity;

            // Rotación del pájaro
            bird.rotation = Math.min(Math.max(bird.velocity * 3, -25), 90);

            // Limitar a los bordes
            if (bird.y + bird.height > canvas.height - 30) {
                gameOver();
            }
            if (bird.y < 0) {
                bird.y = 0;
                bird.velocity = 0;
            }
        }

        function updatePipes() {
            // Crear nuevas tuberías (limitadas por maxObstacles)
            if (pipes.length < game.maxObstacles && 
                (pipes.length === 0 || pipes[pipes.length - 1].x < canvas.width - pipeSpacing)) {
                createPipe();
            }

            // Mover y actualizar tuberías
            for (let i = pipes.length - 1; i >= 0; i--) {
                pipes[i].x -= game.pipeSpeed;

                // Marcar puntos
                if (!pipes[i].passed && pipes[i].x + pipeWidth < bird.x) {
                    pipes[i].passed = true;
                    game.score++;
                    document.getElementById('currentScore').textContent = game.score;

                    if (game.score > game.highScore) {
                        game.highScore = game.score;
                        localStorage.setItem('flappyHighScore', game.highScore);
                        document.getElementById('highScore').textContent = game.highScore;
                    }
                }

                // Eliminar tuberías fuera de pantalla
                if (pipes[i].x + pipeWidth < 0) {
                    pipes.splice(i, 1);
                }

                // Colisión
                if (checkCollision(pipes[i])) {
                    gameOver();
                }
            }
        }

        function checkCollision(pipe) {
            // Colisión con tubería superior
            if (
                bird.x + bird.width > pipe.x &&
                bird.x < pipe.x + pipeWidth &&
                bird.y < pipe.top
            ) {
                return true;
            }

            // Colisión con tubería inferior
            if (
                bird.x + bird.width > pipe.x &&
                bird.x < pipe.x + pipeWidth &&
                bird.y + bird.height > pipe.bottom
            ) {
                return true;
            }

            return false;
        }

        function gameOver() {
            game.isRunning = false;
            document.getElementById('finalScore').textContent = game.score;
            document.getElementById('finalHighScore').textContent = game.highScore;
            document.getElementById('gameOverOverlay').classList.add('active');
        }

        function draw() {
            // Cielo
            const gradient = ctx.createLinearGradient(0, 0, 0, canvas.height);
            gradient.addColorStop(0, '#87CEEB');
            gradient.addColorStop(1, '#E0F6FF');
            ctx.fillStyle = gradient;
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Nubes
            drawClouds();

            // Tuberías
            pipes.forEach(pipe => {
                // Tubería superior
                ctx.fillStyle = colors.pipe;
                ctx.fillRect(pipe.x, 0, pipeWidth, pipe.top);
                ctx.strokeStyle = colors.pipeOutline;
                ctx.lineWidth = 3;
                ctx.strokeRect(pipe.x, 0, pipeWidth, pipe.top);

                // Borde superior
                ctx.fillRect(pipe.x - 5, pipe.top - 20, pipeWidth + 10, 20);
                ctx.strokeRect(pipe.x - 5, pipe.top - 20, pipeWidth + 10, 20);

                // Tubería inferior
                ctx.fillStyle = colors.pipe;
                ctx.fillRect(pipe.x, pipe.bottom, pipeWidth, canvas.height - pipe.bottom);
                ctx.strokeStyle = colors.pipeOutline;
                ctx.strokeRect(pipe.x, pipe.bottom, pipeWidth, canvas.height - pipe.bottom);

                // Borde inferior
                ctx.fillRect(pipe.x - 5, pipe.bottom, pipeWidth + 10, 20);
                ctx.strokeRect(pipe.x - 5, pipe.bottom, pipeWidth + 10, 20);
            });

            // Pájaro
            ctx.save();
            ctx.translate(bird.x + bird.width / 2, bird.y + bird.height / 2);
            ctx.rotate((bird.rotation * Math.PI) / 180);
            
            // Cuerpo
            ctx.fillStyle = colors.bird;
            ctx.beginPath();
            ctx.ellipse(0, 0, bird.width / 2, bird.height / 2, 0, 0, Math.PI * 2);
            ctx.fill();
            
            // Ojo
            ctx.fillStyle = 'white';
            ctx.beginPath();
            ctx.arc(bird.width / 4, -bird.height / 6, 5, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.fillStyle = 'black';
            ctx.beginPath();
            ctx.arc(bird.width / 4, -bird.height / 6, 3, 0, Math.PI * 2);
            ctx.fill();
            
            // Pico
            ctx.fillStyle = '#FF5722';
            ctx.beginPath();
            ctx.moveTo(bird.width / 2, 0);
            ctx.lineTo(bird.width / 2 + 10, -3);
            ctx.lineTo(bird.width / 2 + 10, 3);
            ctx.closePath();
            ctx.fill();
            
            ctx.restore();

            // Suelo
            ctx.fillStyle = colors.ground;
            ctx.fillRect(0, canvas.height - 30, canvas.width, 30);
            ctx.strokeStyle = '#8B7355';
            ctx.lineWidth = 2;
            ctx.strokeRect(0, canvas.height - 30, canvas.width, 30);
        }

        function drawClouds() {
            ctx.fillStyle = 'rgba(255, 255, 255, 0.5)';
            
            // Nube 1
            ctx.beginPath();
            ctx.arc(100, 80, 20, 0, Math.PI * 2);
            ctx.arc(120, 70, 25, 0, Math.PI * 2);
            ctx.arc(140, 80, 20, 0, Math.PI * 2);
            ctx.fill();

            // Nube 2
            ctx.beginPath();
            ctx.arc(canvas.width - 120, 120, 25, 0, Math.PI * 2);
            ctx.arc(canvas.width - 95, 110, 30, 0, Math.PI * 2);
            ctx.arc(canvas.width - 70, 120, 25, 0, Math.PI * 2);
            ctx.fill();
        }

        function gameLoop() {
            if (!game.isRunning || game.isPaused) return;

            updateBird();
            updatePipes();
            draw();

            requestAnimationFrame(gameLoop);
        }

        // Inicialización al cargar la página
        async function init() {
            document.getElementById('highScore').textContent = game.highScore;
            document.getElementById('levelSelect').value = nivell;
            
            const success = await loadLevelConfig(nivell);
            
            // Ocultar loading
            document.getElementById('loadingOverlay').style.display = 'none';
            
            if (success) {
                draw();
            }
        }

        // Iniciar cuando se carga la página
        init();
    </script>
</body>
</html>