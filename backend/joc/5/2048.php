<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../index.php'); // o donde esté el login
    exit;
}

// Simulamos datos del juego
$juego_info = [
    'nombre' => '2048',
    'descripcion' => 'Combina fichas del mismo número para alcanzar el 2048. Usa las flechas del teclado para mover las fichas',
    'record' => 0
];
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>🎮 2048 - Game Portal</title>
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
    max-width: 600px;
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
.game-status {
    text-align: center;
    padding: 15px;
    background-color: #334155;
    border-radius: 10px;
    border: 2px solid #475569;
    margin-bottom: 20px;
    font-size: 14px;
    color: #cbd5e1;
}
.grid-container {
    position: relative;
    background-color: #475569;
    padding: 15px;
    border-radius: 10px;
    width: 100%;
    max-width: 500px;
    aspect-ratio: 1;
    margin: 0 auto;
    overflow: hidden;
}
.grid {
    position: absolute;
    width: calc(100% - 30px);
    height: calc(100% - 30px);
    top: 15px;
    left: 15px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 10px;
}
.cell {
    background-color: #334155;
    border-radius: 5px;
}
.tile {
    position: absolute;
    width: calc(25% - 10px);
    height: calc(25% - 10px);
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
    font-size: 32px;
    font-weight: bold;
    transition: transform 0.15s ease, background-color 0.2s ease;
    z-index: 10;
}
.tile-2 { background-color: #eee4da; color: #776e65; }
.tile-4 { background-color: #ede0c8; color: #776e65; }
.tile-8 { background-color: #f2b179; color: #f9f6f2; }
.tile-16 { background-color: #f59563; color: #f9f6f2; }
.tile-32 { background-color: #f67c5f; color: #f9f6f2; }
.tile-64 { background-color: #f65e3b; color: #f9f6f2; }
.tile-128 { background-color: #edcf72; color: #f9f6f2; font-size: 28px; }
.tile-256 { background-color: #edcc61; color: #f9f6f2; font-size: 28px; }
.tile-512 { background-color: #edc850; color: #f9f6f2; font-size: 28px; }
.tile-1024 { background-color: #edc53f; color: #f9f6f2; font-size: 24px; }
.tile-2048 { background-color: #edc22e; color: #f9f6f2; font-size: 24px; }
.tile-4096 { background-color: #3c3a32; color: #f9f6f2; font-size: 24px; }
.tile-new { animation: appear 0.2s ease; }
.tile-merged { animation: pop 0.2s ease; }
@keyframes appear { 0% { transform: scale(0); } 100% { transform: scale(1); } }
@keyframes pop { 50% { transform: scale(1.2); } 100% { transform: scale(1); } }

.game-overlay {
    position: absolute;
    top: 0; left: 0; right: 0; bottom: 0;
    background: rgba(15, 23, 42, 0.95);
    border-radius: 10px;
    display: none;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    text-align: center;
    padding: 20px;
}
.game-overlay.active { display: flex; }
.overlay-title {
    font-size: 48px;
    margin-bottom: 20px;
    background: linear-gradient(135deg, #6366f1, #ec4899);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.overlay-message { font-size: 20px; margin-bottom: 15px; color: #cbd5e1; }
.overlay-score { font-size: 16px; margin-bottom: 30px; color: #94a3b8; }
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
.instructions {
    background-color: #334155;
    padding: 20px;
    border-radius: 10px;
    border-left: 4px solid #6366f1;
    margin-top: 20px;
    font-size: 14px;
    color: #cbd5e1;
}
.instructions h4 { color: #6366f1; margin-bottom: 10px; font-size: 16px; }
.controls-visual {
    display: flex;
    justify-content: center;
    margin-top: 15px;
    gap: 10px;
}
.arrow-key {
    width: 40px; height: 40px;
    background-color: #475569;
    border: 2px solid #6366f1;
    border-radius: 5px;
    display: flex; align-items: center; justify-content: center;
    font-size: 20px; color: #6366f1;
}
</style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1>🎮 2048</h1>
        <p><?= htmlspecialchars($juego_info['descripcion']); ?></p>
    </div>

    <div class="game-wrapper">
        <div class="score-board">
            <div class="score-item">
                <div class="score-label">Puntuación</div>
                <div class="score-value" id="currentScore">0</div>
            </div>
            <div class="score-item">
                <div class="score-label">🏆 Récord</div>
                <div class="score-value record" id="highScore">0</div>
            </div>
        </div>

        <div class="game-status">⌨️ Usa las flechas del teclado para jugar</div>

        <div class="game-container">
            <div class="grid-container">
                <div class="grid" id="grid"></div>
            </div>
            <div class="game-overlay" id="gameOverOverlay">
                <div class="overlay-title">Game Over</div>
                <div class="overlay-message">¡No hay más movimientos!</div>
                <div class="overlay-score">Puntuación final: <span id="finalScore">0</span></div>
                <button class="btn-primary" onclick="newGame()">🔄 Nueva Partida</button>
            </div>
            <div class="game-overlay" id="winOverlay">
                <div class="overlay-title">🎉 ¡Ganaste!</div>
                <div class="overlay-message">¡Llegaste a 2048!</div>
                <div class="overlay-score">Puntuación: <span id="winScore">0</span></div>
                <div style="display: flex; gap: 10px;">
                    <button class="btn-primary" onclick="continueGame()">▶ Continuar</button>
                    <button class="btn-secondary" onclick="newGame()">🔄 Nueva Partida</button>
                </div>
            </div>
        </div>

        <div class="buttons-container" style="display:flex;gap:15px;justify-content:center;margin-top:20px;">
            <button class="btn-secondary" onclick="newGame()">🔄 Nueva Partida</button>
            <button class="btn-secondary" onclick="undoMove()" id="undoBtn">↶ Deshacer</button>
            <a href="index.php" class="btn-back">← Volver</a>
        </div>

        <div class="instructions">
            <h4>📋 Cómo Jugar</h4>
            <ul>
                <li>Usa las <strong>flechas del teclado</strong> (↑ ↓ ← →) para mover las fichas.</li>
                <li>Cuando dos fichas con el mismo número se tocan, se <strong>fusionan</strong>.</li>
                <li>¡Crea una ficha con el número <strong>2048</strong> para ganar!</li>
                <li>El juego termina cuando no quedan movimientos posibles.</li>
                <li>Puedes continuar jugando después de alcanzar 2048.</li>
            </ul>
            <div class="controls-visual">
                <div class="arrow-key">↑</div>
                <div class="arrow-key">↓</div>
                <div class="arrow-key">←</div>
                <div class="arrow-key">→</div>
            </div>
        </div>
    </div>
</div>

<script>
let grid = [], score = 0, highScore = parseInt(localStorage.getItem('2048HighScore')) || 0;
let hasWon = false, previousState = null, canUndo = false;

function initGame() {
    grid = Array(4).fill().map(() => Array(4).fill(0));
    score = 0; hasWon = false; canUndo = false; previousState = null;
    document.getElementById('currentScore').textContent = score;
    document.getElementById('highScore').textContent = highScore;
    document.getElementById('gameOverOverlay').classList.remove('active');
    document.getElementById('winOverlay').classList.remove('active');
    document.getElementById('undoBtn').disabled = true;
    addNewTile(); addNewTile(); updateDisplay(false);
}

function newGame() { initGame(); }
function continueGame() { document.getElementById('winOverlay').classList.remove('active'); }

function addNewTile() {
    const empty = [];
    for (let i=0;i<4;i++) for (let j=0;j<4;j++) if (grid[i][j]==0) empty.push({i,j});
    if (empty.length>0){
        const {i,j}=empty[Math.floor(Math.random()*empty.length)];
        grid[i][j]=Math.random()<0.9?2:4;
    }
}

function updateDisplay(animated=true){
    const gridContainer=document.querySelector('.grid-container');
    gridContainer.querySelectorAll('.tile').forEach(t=>t.remove());
    const size=gridContainer.clientWidth/4;
    const gap=10;

    for(let i=0;i<4;i++){
        for(let j=0;j<4;j++){
            const val=grid[i][j];
            if(val!==0){
                const tile=document.createElement('div');
                tile.className=`tile tile-${val}`;
                tile.textContent=val;
                const x=j*(size+gap/2);
                const y=i*(size+gap/2);
                tile.style.transform=`translate(${x}px,${y}px)`;
                if(!animated) tile.style.transition='none';
                gridContainer.appendChild(tile);
            }
        }
    }
    document.getElementById('currentScore').textContent=score;
}

function saveState(){
    previousState={grid:grid.map(r=>[...r]),score};
    canUndo=true;
    document.getElementById('undoBtn').disabled=false;
}

function undoMove(){
    if(canUndo&&previousState){
        grid=previousState.grid.map(r=>[...r]);
        score=previousState.score;
        canUndo=false;
        document.getElementById('undoBtn').disabled=true;
        updateDisplay(false);
    }
}

function move(direction){
    saveState();
    let moved=false;
    const newGrid=grid.map(r=>[...r]);
    const merge=(arr)=>{
        arr=arr.filter(v=>v!==0);
        for(let k=0;k<arr.length-1;k++){
            if(arr[k]===arr[k+1]){
                arr[k]*=2;
                score+=arr[k];
                arr.splice(k+1,1);
                if(arr[k]===2048&&!hasWon){hasWon=true;setTimeout(showWin,300);}
            }
        }
        while(arr.length<4) arr.push(0);
        return arr;
    };
    if(direction==='left'||direction==='right'){
        for(let i=0;i<4;i++){
            let row=newGrid[i];
            if(direction==='right')row.reverse();
            let merged=merge(row);
            if(direction==='right')merged.reverse();
            if(JSON.stringify(merged)!==JSON.stringify(newGrid[i])) moved=true;
            newGrid[i]=merged;
        }
    } else {
        for(let j=0;j<4;j++){
            let col=[newGrid[0][j],newGrid[1][j],newGrid[2][j],newGrid[3][j]];
            if(direction==='down')col.reverse();
            let merged=merge(col);
            if(direction==='down')merged.reverse();
            for(let i=0;i<4;i++){
                if(newGrid[i][j]!==merged[i]) moved=true;
                newGrid[i][j]=merged[i];
            }
        }
    }
    if(moved){
        grid=newGrid;
        addNewTile();
        updateDisplay(true);
        if(score>highScore){
            highScore=score;
            localStorage.setItem('2048HighScore',highScore);
            document.getElementById('highScore').textContent=highScore;
        }
        if(!canMove()) setTimeout(showGameOver,300);
    } else {
        canUndo=false;
        document.getElementById('undoBtn').disabled=true;
    }
}

function canMove(){
    for(let i=0;i<4;i++)for(let j=0;j<4;j++){
        if(grid[i][j]===0) return true;
        if(j<3&&grid[i][j]===grid[i][j+1]) return true;
        if(i<3&&grid[i][j]===grid[i+1][j]) return true;
    }
    return false;
}
function showGameOver(){
    document.getElementById('finalScore').textContent=score;
    document.getElementById('gameOverOverlay').classList.add('active');
}
function showWin(){
    document.getElementById('winScore').textContent=score;
    document.getElementById('winOverlay').classList.add('active');
}

document.addEventListener('keydown',e=>{
    if(document.getElementById('gameOverOverlay').classList.contains('active'))return;
    switch(e.key){
        case'ArrowUp':e.preventDefault();move('up');break;
        case'ArrowDown':e.preventDefault();move('down');break;
        case'ArrowLeft':e.preventDefault();move('left');break;
        case'ArrowRight':e.preventDefault();move('right');break;
    }
});
window.onload=initGame;
</script>
</body>
</html>
