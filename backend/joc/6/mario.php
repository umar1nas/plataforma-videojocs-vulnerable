<?php
$juego_info = [
    'nombre' => 'Super Jump',
    'descripcion' => 'Salta, evita enemigos y recoge monedas. ¡Completa todos los niveles!'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍄 Super Jump - Game Portal</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0f172a;
            color: #f1f5f9;
            overflow: hidden;
        }
        .top-bar {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.3);
        }
        .game-title { font-size: 24px; font-weight: bold; }
        .stats { display: flex; gap: 30px; align-items: center; }
        .stat-item { display: flex; align-items: center; gap: 8px; font-size: 18px; font-weight: bold; }
        canvas { display: block; background: #87CEEB; }
        .game-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(15, 23, 42, 0.95);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 200;
        }
        .game-overlay.active { display: flex; }
        .overlay-content {
            background: linear-gradient(135deg, #1e293b, #334155);
            padding: 50px;
            border-radius: 20px;
            text-align: center;
            border: 3px solid #6366f1;
            box-shadow: 0 10px 50px rgba(0, 0, 0, 0.5);
        }
        .overlay-title {
            font-size: 48px;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .overlay-message { font-size: 20px; margin-bottom: 30px; color: #cbd5e1; }
        button {
            padding: 15px 30px;
            font-size: 16px;
            font-weight: bold;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            color: white;
            margin: 5px;
        }
        button:hover { transform: translateY(-2px); }
        .btn-secondary {
            background-color: #475569;
            border: 2px solid #6366f1;
        }
        .controls-help {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(15, 23, 42, 0.9);
            padding: 15px 30px;
            border-radius: 10px;
            border: 2px solid #6366f1;
            font-size: 14px;
            color: #cbd5e1;
        }
    </style>
</head>
<body>
    <div class="top-bar">
        <div class="game-title">🍄 Super Jump</div>
        <div class="stats">
            <div class="stat-item"><span>🏆</span><span id="score">0</span></div>
            <div class="stat-item"><span>🪙</span><span id="coins">0</span></div>
            <div class="stat-item"><span>Nivel <span id="level">1</span></span></div>
            <div class="stat-item"><span id="lives">❤️❤️❤️</span></div>
        </div>
    </div>
    <canvas id="gameCanvas"></canvas>
    <div class="controls-help">
        <strong>←→</strong> Mover | <strong>ESPACIO</strong> Saltar | <strong>P</strong> Pausa | <strong>R</strong> Reiniciar
    </div>

    <!-- Overlays -->
    <div class="game-overlay active" id="startOverlay">
        <div class="overlay-content">
            <div class="overlay-title">🍄 Super Jump</div>
            <div class="overlay-message">¡Recoge monedas, evita enemigos y llega a la bandera!</div>
            <button onclick="startGame()">▶ Comenzar</button>
            <a href="index.php"><button class="btn-secondary">← Volver</button></a>
        </div>
    </div>

    <div class="game-overlay" id="pauseOverlay">
        <div class="overlay-content">
            <div class="overlay-title">⏸ Pausa</div>
            <button onclick="resumeGame()">▶ Continuar</button>
            <button class="btn-secondary" onclick="restartLevel()">🔄 Reiniciar</button>
        </div>
    </div>

    <div class="game-overlay" id="gameOverOverlay">
        <div class="overlay-content">
            <div class="overlay-title">💀 Game Over</div>
            <div class="overlay-message">Puntuación: <span id="finalScore">0</span></div>
            <button onclick="restartGame()">🔄 Reintentar</button>
            <a href="index.php"><button class="btn-secondary">← Salir</button></a>
        </div>
    </div>

    <div class="game-overlay" id="levelCompleteOverlay">
        <div class="overlay-content">
            <div class="overlay-title">🎉 ¡Nivel Completado!</div>
            <div class="overlay-message">Puntuación: <span id="levelScore">0</span></div>
            <button onclick="nextLevel()">▶ Siguiente Nivel</button>
        </div>
    </div>

    <div class="game-overlay" id="gameCompleteOverlay">
        <div class="overlay-content">
            <div class="overlay-title">🏆 ¡Victoria!</div>
            <div class="overlay-message">¡Completaste todos los niveles!<br>Puntuación: <span id="totalScore">0</span></div>
            <button onclick="restartGame()">🔄 Jugar de Nuevo</button>
            <a href="index.php"><button class="btn-secondary">← Salir</button></a>
        </div>
    </div>

    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = window.innerWidth;
        canvas.height = window.innerHeight - 60;

        const game = {
            isRunning: false,
            isPaused: false,
            currentLevel: 1,
            maxLevels: 5,
            score: 0,
            coins: 0,
            lives: 3,
            gravity: 0.6,
            cameraX: 0
        };

        const player = {
            x: 100, y: 100, width: 32, height: 32,
            velocityX: 0, velocityY: 0,
            speed: 5, jumpPower: -15,
            onGround: false, direction: 1,
            invincible: false, invincibleTimer: 0
        };

        const keys = {};
        document.addEventListener('keydown', (e) => {
            keys[e.key.toLowerCase()] = true;
            if (e.key === ' ' && game.isRunning && !game.isPaused && player.onGround) {
                e.preventDefault();
                player.velocityY = player.jumpPower;
                player.onGround = false;
            }
            if (e.key.toLowerCase() === 'p' && game.isRunning) togglePause();
            if (e.key.toLowerCase() === 'r' && game.isRunning) restartLevel();
        });
        document.addEventListener('keyup', (e) => keys[e.key.toLowerCase()] = false);

        const levels = [
            // NIVEL 1
            {
                platforms: [
                    {x:0,y:500,w:800,h:40}, {x:900,y:500,w:300,h:40}, {x:1300,y:500,w:400,h:40}, 
                    {x:1800,y:400,w:200,h:40}, {x:2100,y:350,w:300,h:40}, {x:2500,y:500,w:500,h:40}
                ],
                enemies: [
                    {x:400,y:468,w:32,h:32,vx:2,min:200,max:600}, {x:1000,y:468,w:32,h:32,vx:2,min:900,max:1200}
                ],
                coins: [
                    {x:300,y:400,c:false}, {x:500,y:350,c:false}, {x:1100,y:400,c:false}, {x:1900,y:300,c:false}, {x:2700,y:400,c:false}
                ],
                flag:{x:2850,y:380}
            },
            // NIVEL 2
            {
                platforms: [
                    {x:0,y:500,w:400,h:40}, {x:550,y:450,w:200,h:40}, {x:850,y:400,w:150,h:40},
                    {x:1100,y:350,w:150,h:40}, {x:1350,y:400,w:200,h:40}, {x:1900,y:350,w:200,h:40}, {x:2600,y:500,w:500,h:40}
                ],
                enemies: [
                    {x:600,y:418,w:32,h:32,vx:1.5,min:550,max:750}, {x:1150,y:318,w:32,h:32,vx:1.5,min:1100,max:1250}, {x:2700,y:468,w:32,h:32,vx:2,min:2600,max:2900}
                ],
                coins: [
                    {x:200,y:400,c:false}, {x:650,y:350,c:false}, {x:1150,y:250,c:false}, {x:2000,y:250,c:false}, {x:2800,y:400,c:false}
                ],
                flag:{x:2950,y:380}
            },
            // NIVEL 3
            {
                platforms: [
                    {x:0,y:500,w:350,h:40}, {x:500,y:450,w:100,h:40}, {x:700,y:400,w:100,h:40}, {x:900,y:350,w:150,h:40},
                    {x:1150,y:300,w:100,h:40}, {x:1550,y:400,w:150,h:40}, {x:2000,y:250,w:150,h:40}, {x:2800,y:500,w:400,h:40}
                ],
                enemies: [
                    {x:200,y:468,w:32,h:32,vx:3,min:100,max:350}, {x:950,y:318,w:32,h:32,vx:2,min:900,max:1050}, {x:2900,y:468,w:32,h:32,vx:3,min:2800,max:3100}
                ],
                coins: [
                    {x:550,y:350,c:false}, {x:1000,y:250,c:false}, {x:1600,y:300,c:false}, {x:2100,y:150,c:false}, {x:2950,y:400,c:false}
                ],
                flag:{x:3050,y:380}
            },
            // NIVEL 4
            {
                platforms: [
                    {x:0,y:500,w:300,h:40}, {x:400,y:450,w:120,h:40}, {x:650,y:380,w:100,h:40}, {x:950,y:320,w:120,h:40},
                    {x:1300,y:280,w:100,h:40}, {x:1800,y:400,w:200,h:40}, {x:2400,y:300,w:150,h:40}, {x:3000,y:500,w:400,h:40}
                ],
                enemies: [
                    {x:250,y:468,w:32,h:32,vx:3,min:100,max:300}, {x:700,y:348,w:32,h:32,vx:3,min:650,max:800}, 
                    {x:1850,y:368,w:32,h:32,vx:3.5,min:1800,max:2000}, {x:2900,y:468,w:32,h:32,vx:3.5,min:2800,max:3100}
                ],
                coins: [
                    {x:500,y:300,c:false}, {x:1000,y:250,c:false}, {x:1400,y:200,c:false}, {x:1900,y:300,c:false},
                    {x:2500,y:200,c:false}, {x:3100,y:400,c:false}
                ],
                flag:{x:3250,y:380}
            },
            // NIVEL 5
            {
                platforms: [
                    {x:0,y:500,w:250,h:40},{x:400,y:430,w:100,h:40},{x:700,y:370,w:100,h:40},{x:1000,y:310,w:100,h:40},
                    {x:1300,y:250,w:100,h:40},{x:1600,y:400,w:150,h:40},{x:1900,y:300,w:100,h:40},{x:2300,y:250,w:100,h:40},
                    {x:2700,y:200,w:150,h:40},{x:3300,y:500,w:400,h:40}
                ],
                enemies: [
                    {x:200,y:468,w:32,h:32,vx:4,min:100,max:250},{x:750,y:338,w:32,h:32,vx:3.5,min:700,max:850},
                    {x:1350,y:218,w:32,h:32,vx:4,min:1300,max:1450},{x:1950,y:268,w:32,h:32,vx:4,min:1900,max:2100},
                    {x:2750,y:168,w:32,h:32,vx:4.5,min:2700,max:2900}
                ],
                coins: [
                    {x:450,y:350,c:false},{x:850,y:280,c:false},{x:1150,y:200,c:false},{x:1750,y:250,c:false},
                    {x:2150,y:180,c:false},{x:2600,y:130,c:false},{x:3400,y:400,c:false}
                ],
                flag:{x:3550,y:380}
            }
        ];

        let currentLevel = null;

        function startGame(){
            game.isRunning=true;
            game.currentLevel=1;
            game.score=0;
            game.coins=0;
            game.lives=3;
            document.getElementById('startOverlay').classList.remove('active');
            loadLevel(game.currentLevel);
            updateUI();
            gameLoop();
        }

        function loadLevel(num){
            currentLevel = JSON.parse(JSON.stringify(levels[num-1]));
            player.x=100;
            player.y=100;
            player.velocityX=0;
            player.velocityY=0;
            player.onGround=false;
            game.cameraX=0;
        }

        function nextLevel(){
            game.currentLevel++;
            if(game.currentLevel>game.maxLevels){
                completeGame();
            } else {
                document.getElementById('levelCompleteOverlay').classList.remove('active');
                loadLevel(game.currentLevel);
                updateUI();
                game.isRunning=true;
                gameLoop();
            }
        }

        function restartLevel(){
            document.getElementById('pauseOverlay').classList.remove('active');
            loadLevel(game.currentLevel);
            updateUI();
            game.isPaused=false;
            game.isRunning=true;
            gameLoop();
        }

        function restartGame(){
            document.getElementById('gameOverOverlay').classList.remove('active');
            document.getElementById('gameCompleteOverlay').classList.remove('active');
            game.currentLevel=1;
            game.score=0;
            game.coins=0;
            game.lives=3;
            loadLevel(game.currentLevel);
            updateUI();
            game.isRunning=true;
            gameLoop();
        }

        function completeGame(){
            document.getElementById('gameCompleteOverlay').classList.add('active');
            document.getElementById('totalScore').textContent=game.score;
            game.isRunning=false;
        }

        function togglePause(){
            game.isPaused=!game.isPaused;
            document.getElementById('pauseOverlay').classList.toggle('active',game.isPaused);
        }

        function updateUI(){
            document.getElementById('score').textContent=game.score;
            document.getElementById('coins').textContent=game.coins;
            document.getElementById('level').textContent=game.currentLevel;
            document.getElementById('lives').textContent='❤️'.repeat(game.lives);
        }

        function gameLoop(){
            if(!game.isRunning || game.isPaused) return;
            ctx.clearRect(0,0,canvas.width,canvas.height);

            // Aplicar gravedad
            player.velocityY+=game.gravity;
            player.y+=player.velocityY;
            player.x+=player.velocityX;

            // Colisiones con plataformas
            currentLevel.platforms.forEach(p=>{
                if(player.x+player.width>p.x && player.x<p.x+p.w && player.y+player.height>p.y && player.y+player.height<p.y+p.h){
                    player.y=p.y-player.height;
                    player.velocityY=0;
                    player.onGround=true;
                }
            });

            // Dibujar plataformas
            ctx.fillStyle='#333';
            currentLevel.platforms.forEach(p=>ctx.fillRect(p.x-game.cameraX,p.y,p.w,p.h));

            // Dibujar player
            ctx.fillStyle='#ff0';
            ctx.fillRect(player.x-game.cameraX,player.y,player.width,player.height);

            // Dibujar monedas
            ctx.fillStyle='#f90';
            currentLevel.coins.forEach(c=>{
                if(!c.c){
                    ctx.beginPath();
                    ctx.arc(c.x-game.cameraX,c.y,8,0,Math.PI*2);
                    ctx.fill();
                }
            });

            // Coin collection
            currentLevel.coins.forEach(c=>{
                if(!c.c && player.x< c.x+8 && player.x+player.width>c.x-8 && player.y< c.y+8 && player.y+player.height>c.y-8){
                    c.c=true;
                    game.coins++;
                    game.score+=10;
                    updateUI();
                }
            });

            // Dibujar bandera
            ctx.fillStyle='green';
            ctx.fillRect(currentLevel.flag.x-game.cameraX,currentLevel.flag.y,40,60);

            // Check level complete
            if(player.x+player.width>currentLevel.flag.x){
                document.getElementById('levelScore').textContent=game.score;
                document.getElementById('levelCompleteOverlay').classList.add('active');
                game.isRunning=false;
            }

            // Movimiento izquierda/derecha
            player.velocityX=0;
            if(keys['arrowleft'] || keys['a']) player.velocityX=-player.speed;
            if(keys['arrowright'] || keys['d']) player.velocityX=player.speed;

            // Camera follow
            game.cameraX=player.x-200;

            requestAnimationFrame(gameLoop);
        }
    </script>
</body>
</html>
