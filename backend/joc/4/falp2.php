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

// Obtener configuración de todos los niveles desde la API
$niveles_config = [];

for ($i = 1; $i <= 5; $i++) {
    $api_url = "http://" . $_SERVER['HTTP_HOST'] . "/api.php?jocs=" . $joc_id . "&nivells=" . $i;
    
    $response = @file_get_contents($api_url);
    
    if ($response !== false) {
        $config_data = json_decode($response, true);
        
        if ($config_data && isset($config_data['velocidad']) && isset($config_data['obstaculos'])) {
            $niveles_config[$i] = [
                'velocidad' => (int)$config_data['velocidad'],
                'obstaculos' => (int)$config_data['obstaculos'],
                'puntuacio_minima' => isset($config_data['puntuacio_minima']) ? (int)$config_data['puntuacio_minima'] : ($i * 10),
                'nom' => 'Nivel ' . $i
            ];
        }
    }
}

// Fallback: Si la API falla, usar configuración por defecto
if (empty($niveles_config)) {
    $niveles_config = [
        1 => ['velocidad' => 5, 'obstaculos' => 10, 'puntuacio_minima' => 10, 'nom' => 'Nivel 1'],
        2 => ['velocidad' => 6, 'obstaculos' => 15, 'puntuacio_minima' => 20, 'nom' => 'Nivel 2'],
        3 => ['velocidad' => 7, 'obstaculos' => 20, 'puntuacio_minima' => 30, 'nom' => 'Nivel 3'],
        4 => ['velocidad' => 8, 'obstaculos' => 25, 'puntuacio_minima' => 40, 'nom' => 'Nivel 4'],
        5 => ['velocidad' => 9, 'obstaculos' => 30, 'puntuacio_minima' => 50, 'nom' => 'Nivel 5']
    ];
}

// Fallback: Si la API falla, usar configuración por defecto
if (empty($niveles_config)) {
    $niveles_config = [
        1 => ['velocidad' => 5, 'obstaculos' => 10, 'puntuacio_minima' => 10, 'nom' => 'Nivel 1'],
        2 => ['velocidad' => 6, 'obstaculos' => 15, 'puntuacio_minima' => 20, 'nom' => 'Nivel 2'],
        3 => ['velocidad' => 7, 'obstaculos' => 20, 'puntuacio_minima' => 30, 'nom' => 'Nivel 3'],
        4 => ['velocidad' => 8, 'obstaculos' => 25, 'puntuacio_minima' => 40, 'nom' => 'Nivel 4'],
        5 => ['velocidad' => 9, 'obstaculos' => 30, 'puntuacio_minima' => 50, 'nom' => 'Nivel 5']
    ];
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Flappy Bird - Plataforma de Videojocs</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to bottom, #4ec0ca 0%, #87ceeb 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            overflow: hidden;
        }
        
        #gameContainer {
            position: relative;
            width: 400px;
            height: 600px;
            background: linear-gradient(to bottom, #4ec0ca 0%, #87ceeb 70%, #ded895 70%, #ded895 100%);
            border: 3px solid #333;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        
        #gameCanvas {
            display: block;
            width: 100%;
            height: 100%;
        }
        
        #ui {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
        }
        
        #score {
            position: absolute;
            top: 20px;
            left: 20px;
            font-size: 24px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        #timer {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 20px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        #level {
            position: absolute;
            top: 50px;
            left: 20px;
            font-size: 18px;
            font-weight: bold;
            color: white;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        
        #startScreen, #gameOverScreen, #levelCompleteScreen {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            pointer-events: all;
        }
        
        #startScreen h1, #gameOverScreen h1, #levelCompleteScreen h1 {
            font-size: 48px;
            margin-bottom: 20px;
            text-shadow: 3px 3px 6px rgba(0,0,0,0.8);
        }
        
        #startScreen p, #gameOverScreen p, #levelCompleteScreen p {
            font-size: 20px;
            margin-bottom: 10px;
            text-align: center;
            padding: 0 20px;
        }
        
        .btn {
            margin-top: 20px;
            padding: 15px 40px;
            font-size: 20px;
            font-weight: bold;
            color: white;
            background: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        
        .btn:hover {
            background: #45a049;
            transform: translateY(-2px);
            box-shadow: 0 6px 8px rgba(0,0,0,0.4);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .hidden {
            display: none !important;
        }
        
        #finalScore {
            font-size: 32px;
            color: #ffeb3b;
            margin: 10px 0;
        }
        
        #requiredScore {
            font-size: 18px;
            color: #ff9800;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div id="gameContainer">
        <canvas id="gameCanvas" width="400" height="600"></canvas>
        
        <div id="ui">
            <div id="score">Puntuació: 0</div>
            <div id="timer">Temps: 0s</div>
            <div id="level">Nivel: <?php echo $nivell_actual; ?></div>
            
            <div id="startScreen">
                <h1>🐦 Flappy Bird</h1>
                <p id="levelName">Nivel <?php echo $nivell_actual; ?></p>
                <p>Puntuació mínima: <span id="minScore"></span></p>
                <p>Prem ESPAI o clica per començar</p>
                <button class="btn" onclick="startGame()">Començar</button>
            </div>
            
            <div id="gameOverScreen" class="hidden">
                <h1>💀 Game Over!</h1>
                <p id="finalScore">Puntuació: 0</p>
                <p id="requiredScore">Necessitaves: 0 punts</p>
                <button class="btn" onclick="restartGame()">Tornar a intentar</button>
            </div>
            
            <div id="levelCompleteScreen" class="hidden">
                <h1>🎉 Nivel Completat!</h1>
                <p id="completeScore">Puntuació: 0</p>
                <p id="nextLevelInfo"></p>
                <button class="btn" id="nextLevelBtn" onclick="nextLevel()">Següent Nivel</button>
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
                    // Cambiar localhost por la IP de tu VM si es necesario
                    const response = await fetch(`http://192.168.1.148/projecte/backend/api.php?jocs=${JOC_ID}&nivells=${i}`);
                    const data = await response.json();
                    
                    console.log(`Respuesta API Nivel ${i}:`, data);
                    
                    // Guardar la configuración del nivel
                    nivelesConfig[i] = {
                        velocidad: data.velocidad || 5,
                        obstaculos: data.obstaculos || 10,
                        puntuacio_minima: data.puntuacio_minima || (i * 10),
                        nom: `Nivel ${i}`
                    };
                    
                    console.log(`Nivel ${i} - Velocidad: ${nivelesConfig[i].velocidad}, Obstáculos: ${nivelesConfig[i].obstaculos}, Puntuación mínima: ${nivelesConfig[i].puntuacio_minima}`);
                    
                } catch (error) {
                    console.error(`Error cargando nivel ${i} desde API:`, error);
                    // Fallback: configuración por defecto si la API falla
                    nivelesConfig[i] = {
                        velocidad: 4 + i,
                        obstaculos: 5 + (i * 5),
                        puntuacio_minima: i * 10,
                        nom: `Nivel ${i}`
                    };
                }
            }
            
            console.log("Configuración de niveles cargada:", nivelesConfig);
            
            // Una vez cargada la configuración, actualizar la UI
            loadLevelConfig();
        }
        
        // Cargar la configuración cuando se carga la página
        cargarConfiguracionAPI();
        
        // Canvas y contexto
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        
        // Variables del juego
        let bird = {
            x: 80,
            y: 250,
            width: 34,
            height: 24,
            gravity: 0.5,
            velocity: 0,
            jump: -9
        };
        
        let pipes = [];
        let score = 0;
        let gameStarted = false;
        let gameOver = false;
        let startTime = null;
        let elapsedTime = 0;
        let timerInterval = null;
        
        let pipeGap = 150;
        let pipeWidth = 52;
        let pipeSpeed = 3;
        let frameCount = 0;
        let pipesPerLevel = 10;
        let pipesPassed = 0;
        
        // Cargar configuración del nivel actual
        function loadLevelConfig() {
            const config = nivelesConfig[currentLevel];
            if (config) {
                pipeSpeed = config.velocidad;
                pipesPerLevel = config.obstaculos;
                document.getElementById('levelName').textContent = config.nom;
                document.getElementById('minScore').textContent = config.puntuacio_minima;
                document.getElementById('level').textContent = 'Nivel: ' + currentLevel;
            }
        }
        
        loadLevelConfig();
        
        // Event listeners
        document.addEventListener('keydown', (e) => {
            if (e.code === 'Space') {
                e.preventDefault();
                if (!gameStarted) {
                    startGame();
                } else if (!gameOver) {
                    bird.velocity = bird.jump;
                }
            }
        });
        
        canvas.addEventListener('click', () => {
            if (!gameStarted) {
                startGame();
            } else if (!gameOver) {
                bird.velocity = bird.jump;
            }
        });
        
        // Funciones del juego
        function startGame() {
            document.getElementById('startScreen').classList.add('hidden');
            gameStarted = true;
            gameOver = false;
            bird.y = 250;
            bird.velocity = 0;
            pipes = [];
            score = 0;
            pipesPassed = 0;
            frameCount = 0;
            elapsedTime = 0;
            startTime = Date.now();
            
            loadLevelConfig();
            
            // Iniciar temporizador
            if (timerInterval) clearInterval(timerInterval);
            timerInterval = setInterval(updateTimer, 1000);
            
            gameLoop();
        }
        
        function updateTimer() {
            if (gameStarted && !gameOver) {
                elapsedTime = Math.floor((Date.now() - startTime) / 1000);
                document.getElementById('timer').textContent = 'Temps: ' + elapsedTime + 's';
            }
        }
        
        function restartGame() {
            document.getElementById('gameOverScreen').classList.add('hidden');
            currentLevel = <?php echo $nivell_actual; ?>; // Resetear al nivel actual del usuario
            startGame();
        }
        
        function nextLevel() {
            document.getElementById('levelCompleteScreen').classList.add('hidden');
            currentLevel++;
            if (currentLevel > 5) {
                alert('¡Felicidades! Has completado todos los niveles!');
                currentLevel = 1;
            }
            startGame();
        }
        
        function createPipe() {
            const minHeight = 50;
            const maxHeight = canvas.height - pipeGap - minHeight - 100;
            const height = Math.floor(Math.random() * (maxHeight - minHeight + 1)) + minHeight;
            
            pipes.push({
                x: canvas.width,
                y: 0,
                width: pipeWidth,
                height: height,
                passed: false
            });
        }
        
        function updatePipes() {
            // Crear nuevas tuberías
            if (frameCount % 90 === 0 && pipesPassed < pipesPerLevel) {
                createPipe();
            }
            
            // Actualizar posición de las tuberías
            for (let i = pipes.length - 1; i >= 0; i--) {
                pipes[i].x -= pipeSpeed;
                
                // Incrementar puntuación si el pájaro pasa la tubería
                if (!pipes[i].passed && pipes[i].x + pipes[i].width < bird.x) {
                    pipes[i].passed = true;
                    score++;
                    pipesPassed++;
                    document.getElementById('score').textContent = 'Puntuació: ' + score;
                }
                
                // Eliminar tuberías fuera de pantalla
                if (pipes[i].x + pipes[i].width < 0) {
                    pipes.splice(i, 1);
                }
            }
        }
        
        function checkCollision() {
            // Colisión con el suelo o techo
            if (bird.y + bird.height >= canvas.height - 100 || bird.y <= 0) {
                return true;
            }
            
            // Colisión con tuberías
            for (let pipe of pipes) {
                if (bird.x + bird.width > pipe.x && bird.x < pipe.x + pipe.width) {
                    if (bird.y < pipe.height || bird.y + bird.height > pipe.height + pipeGap) {
                        return true;
                    }
                }
            }
            
            return false;
        }
        
        function endGame() {
            gameOver = true;
            gameStarted = false;
            clearInterval(timerInterval);
            
            // Guardar partida en la base de datos
            savePartida(currentLevel, score, elapsedTime);
            
            const config = nivelesConfig[currentLevel];
            const minScore = config ? config.puntuacio_minima : 0;
            
            if (pipesPassed >= pipesPerLevel && score >= minScore) {
                // Nivel completado
                document.getElementById('completeScore').textContent = 'Puntuació: ' + score;
                
                if (currentLevel < 5) {
                    document.getElementById('nextLevelInfo').textContent = 'Has superat el nivell! Pots continuar al següent.';
                    document.getElementById('nextLevelBtn').style.display = 'block';
                } else {
                    document.getElementById('nextLevelInfo').textContent = 'Has completat tots els nivells!';
                    document.getElementById('nextLevelBtn').textContent = 'Tornar al Nivel 1';
                    document.getElementById('nextLevelBtn').style.display = 'block';
                }
                
                document.getElementById('levelCompleteScreen').classList.remove('hidden');
            } else {
                // Game over
                document.getElementById('finalScore').textContent = 'Puntuació: ' + score;
                document.getElementById('requiredScore').textContent = 'Necessitaves: ' + minScore + ' punts i passar ' + pipesPerLevel + ' obstacles';
                document.getElementById('gameOverScreen').classList.remove('hidden');
            }
        }
        
        function savePartida(nivel, puntuacion, duracion) {
            const url = `../../save_partida.php?usuari_id=${USUARIO_ID}&joc_id=${JOC_ID}&nivell=${nivel}&puntuacio=${puntuacion}&durada=${duracion}`;
            
            console.log('Guardando partida:', url);
            
            fetch(url)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.text(); // Primero obtener como texto
                })
                .then(text => {
                    console.log('Response text:', text);
                    try {
                        const data = JSON.parse(text); // Intentar parsear como JSON
                        console.log('Partida guardada:', data);
                    } catch (e) {
                        console.error('La respuesta no es JSON válido:', text);
                    }
                })
                .catch(error => {
                    console.error('Error al guardar partida:', error);
                });
        }
        
        function drawBird() {
            ctx.fillStyle = '#FFD700';
            ctx.fillRect(bird.x, bird.y, bird.width, bird.height);
            
            // Ojo
            ctx.fillStyle = 'white';
            ctx.beginPath();
            ctx.arc(bird.x + 25, bird.y + 8, 5, 0, Math.PI * 2);
            ctx.fill();
            
            ctx.fillStyle = 'black';
            ctx.beginPath();
            ctx.arc(bird.x + 26, bird.y + 8, 3, 0, Math.PI * 2);
            ctx.fill();
            
            // Pico
            ctx.fillStyle = '#FF6347';
            ctx.beginPath();
            ctx.moveTo(bird.x + bird.width, bird.y + bird.height / 2);
            ctx.lineTo(bird.x + bird.width + 8, bird.y + bird.height / 2 - 4);
            ctx.lineTo(bird.x + bird.width + 8, bird.y + bird.height / 2 + 4);
            ctx.closePath();
            ctx.fill();
        }
        
        function drawPipes() {
            ctx.fillStyle = '#4CAF50';
            ctx.strokeStyle = '#2E7D32';
            ctx.lineWidth = 3;
            
            for (let pipe of pipes) {
                // Tubería superior
                ctx.fillRect(pipe.x, pipe.y, pipe.width, pipe.height);
                ctx.strokeRect(pipe.x, pipe.y, pipe.width, pipe.height);
                
                // Tubería inferior
                ctx.fillRect(pipe.x, pipe.height + pipeGap, pipe.width, canvas.height - pipe.height - pipeGap - 100);
                ctx.strokeRect(pipe.x, pipe.height + pipeGap, pipe.width, canvas.height - pipe.height - pipeGap - 100);
            }
        }
        
        function drawGround() {
            ctx.fillStyle = '#DEB887';
            ctx.fillRect(0, canvas.height - 100, canvas.width, 100);
            
            // Patrón de hierba
            ctx.fillStyle = '#8B7355';
            for (let i = 0; i < canvas.width; i += 20) {
                ctx.fillRect(i, canvas.height - 100, 10, 5);
            }
        }
        
        function gameLoop() {
            if (!gameStarted || gameOver) return;
            
            // Limpiar canvas
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            
            // Dibujar fondo (cielo y suelo)
            drawGround();
            
            // Actualizar física del pájaro
            bird.velocity += bird.gravity;
            bird.y += bird.velocity;
            
            // Actualizar tuberías
            updatePipes();
            
            // Dibujar elementos
            drawPipes();
            drawBird();
            
            // Comprobar colisión
            if (checkCollision()) {
                endGame();
                return;
            }
            
            // Comprobar si se completó el nivel
            if (pipesPassed >= pipesPerLevel && pipes.length === 0) {
                endGame();
                return;
            }
            
            frameCount++;
            requestAnimationFrame(gameLoop);
        }
    </script>
</body>
</html>