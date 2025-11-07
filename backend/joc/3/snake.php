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
<title>🐍 Snake Infinito</title>
<style>
body{margin:0;display:flex;flex-direction:column;align-items:center;justify-content:center;height:100vh;background:linear-gradient(135deg,#0f172a,#1e293b);color:#f1f5f9;font-family:'Segoe UI',sans-serif;}
canvas{background:#0f172a;border:3px solid #6366f1;margin-bottom:10px;}
#info{display:flex;gap:20px;margin-bottom:10px;}
span{font-size:18px;color:#6366f1;}
button{margin:2px;padding:8px 16px;background:#6366f1;color:white;border:none;border-radius:6px;cursor:pointer;}
button:hover{background:#ec4899;}
#statusText{margin-bottom:10px;color:#10b981;font-weight:bold;}
#maxScoreBox{position:absolute;right:20px;top:20px;background:#334155;padding:15px;border-radius:10px;border:2px solid #6366f1;color:#ec4899;}
</style>
</head>
<body>
<h2>🐍 Snake Infinito - <?php echo htmlspecialchars($usuario); ?></h2>
<div id="info">
<span>Puntos: <span id="score">0</span></span>
<span>Vidas: <span id="vidas">5</span></span>
</div>
<div id="statusText">▶ Presiona ESPACIO para comenzar</div>
<canvas id="snakeCanvas" width="600" height="400"></canvas>
<div>
<button id="startBtn">▶ Comenzar</button>
<button id="pauseBtn" disabled>⏸ Pausar</button>
<button id="reiniciarBtn">🔄 Reiniciar</button>
<button id="volverMenuBtn">🏠 Volver al menú</button>
</div>
<div id="maxScoreBox">Máxima puntuación: <span id="maxScore">0</span></div>

<script>
const canvas=document.getElementById('snakeCanvas');
const ctx=canvas.getContext('2d');

let game={running:false,paused:false,score:0,lives:5,gameOver:false,speed:5};
let snake=[{x:10,y:10}];let dir={x:1,y:0};let fruit={x:15,y:10};const grid=20;
let maxScore = parseInt(localStorage.getItem('snakeMaxScore')) || 0;

document.getElementById('maxScore').textContent = maxScore;

const keys={};
document.addEventListener('keydown',e=>{keys[e.key.toLowerCase()]=true;if(e.key===' ') togglePause();});
document.addEventListener('keyup',e=>keys[e.key.toLowerCase()]=false);

document.getElementById('startBtn').addEventListener('click',startGame);
document.getElementById('pauseBtn').addEventListener('click',togglePause);
document.getElementById('reiniciarBtn').addEventListener('click',reiniciarJuego);
document.getElementById('volverMenuBtn').addEventListener('click',()=>{guardarProgreso();window.location.href='../../plataforma.php';});

function startGame(){if(!game.running){game.running=true;game.paused=false;document.getElementById('startBtn').disabled=true;document.getElementById('pauseBtn').disabled=false;document.getElementById('statusText').textContent='▶ Juego en curso...';loop();}}
function togglePause(){if(game.running){game.paused=!game.paused;document.getElementById('statusText').textContent=game.paused?'⏸ Juego pausado':'▶ Juego en curso...';}}
function reiniciarJuego(){snake=[{x:10,y:10}];dir={x:1,y:0};fruit={x:15,y:10};game.score=0;game.lives=5;game.gameOver=false;game.speed=5;document.getElementById('score').textContent=0;document.getElementById('vidas').textContent=5;document.getElementById('statusText').textContent='▶ Presiona ESPACIO para comenzar';guardarProgreso();}
function guardarProgreso(){localStorage.setItem('snakeMaxScore', maxScore);}

function update(){if(game.paused||game.gameOver)return;
if(keys['w']||keys['arrowup'])dir={x:0,y:-1};
if(keys['s']||keys['arrowdown'])dir={x:0,y:1};
if(keys['a']||keys['arrowleft'])dir={x:-1,y:0};
if(keys['d']||keys['arrowright'])dir={x:1,y:0};

const head={x:snake[0].x+dir.x,y:snake[0].y+dir.y};
snake.unshift(head);

if(head.x===fruit.x && head.y===fruit.y){game.score++;document.getElementById('score').textContent=game.score;fruit={x:Math.floor(Math.random()*canvas.width/grid),y:Math.floor(Math.random()*canvas.height/grid)};game.speed+=0.2;if(game.score>maxScore){maxScore=game.score;document.getElementById('maxScore').textContent=maxScore;}guardarProgreso();}else snake.pop();

if(head.x<0||head.x>=canvas.width/grid||head.y<0||head.y>=canvas.height/grid||snake.slice(1).some(s=>s.x===head.x && s.y===head.y)){
game.lives--;document.getElementById('vidas').textContent=game.lives;if(game.lives<=0){game.gameOver=true;document.getElementById('statusText').textContent='💀 GAME OVER';}else{snake=[{x:10,y:10}];dir={x:1,y:0};}}
}

function draw(){ctx.fillStyle='#0f172a';ctx.fillRect(0,0,canvas.width,canvas.height);ctx.fillStyle='#ec4899';ctx.fillRect(fruit.x*grid,fruit.y*grid,grid,grid);ctx.fillStyle='#6366f1';snake.forEach(s=>ctx.fillRect(s.x*grid,s.y*grid,grid-1,grid-1));}
function loop(){if(game.running){update();draw();setTimeout(loop,1000/game.speed);}}
draw();
</script>
</body>
</html>
