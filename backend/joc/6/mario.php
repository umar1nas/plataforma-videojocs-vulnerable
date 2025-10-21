<!doctype html>
<html lang="es">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width,initial-scale=1" />
<title>Super Pixel Jump — Juego 2D estilo Mario (original)</title>
<style>
  :root{
    --bg1:#0f172a; --bg2:#1e293b; --accent1:#6366f1; --accent2:#ec4899;
    --panel:#1f2937; --muted:#94a3b8; --card:#334155;
  }
  *{box-sizing:border-box;font-family:Inter,ui-sans-serif,system-ui,-apple-system,"Segoe UI",Roboto,"Helvetica Neue",Arial;}
  body{
    margin:0;
    min-height:100vh;
    display:flex;
    align-items:center;
    justify-content:center;
    background:linear-gradient(135deg,var(--bg1),var(--bg2));
    color:#e6eef8;
    padding:20px;
  }

  .app {
    width:100%;
    max-width:1000px;
    border-radius:12px;
    overflow:hidden;
    box-shadow:0 10px 40px rgba(2,6,23,0.6);
    background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(0,0,0,0.18));
    border:1px solid rgba(255,255,255,0.03);
    display:grid;
    grid-template-columns: 360px 1fr;
  }

  .left {
    padding:20px;
    background:linear-gradient(135deg,var(--accent1),var(--accent2));
    color:white;
    display:flex;
    flex-direction:column;
    gap:12px;
  }

  .left h1{margin:0;font-size:22px;letter-spacing:0.6px}
  .meta {font-size:13px;opacity:0.95}
  .scoreboard {
    background:rgba(255,255,255,0.06);
    border-radius:10px;padding:12px;display:flex;gap:10px;align-items:center;
    border:1px solid rgba(255,255,255,0.06);
  }
  .stat {
    flex:1;text-align:center;
  }
  .stat .label{font-size:11px;color:rgba(255,255,255,0.85);text-transform:uppercase}
  .stat .value{font-weight:800;font-size:20px;margin-top:6px}

  .controls {
    margin-top:auto;
    font-size:13px;
    background:rgba(0,0,0,0.12);
    padding:10px;border-radius:8px;
  }
  .controls p{margin:6px 0}
  .btns{display:flex;gap:8px;margin-top:8px}
  .btn{
    padding:8px 12px;border-radius:8px;background:rgba(255,255,255,0.12);border:1px solid rgba(255,255,255,0.06);
    cursor:pointer;color:white;font-weight:700;
  }

  .right {
    padding:18px; background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(0,0,0,0.08));
    display:flex;flex-direction:column;gap:12px;
  }

  .canvas-wrap {
    background: linear-gradient(180deg,#88b7ff11,#00000011);
    border-radius:10px; overflow:hidden; border:1px solid rgba(255,255,255,0.03);
    position:relative;
  }

  canvas{
    display:block;
    width:100%;
    height:560px;
    background: linear-gradient(180deg,#7cc1ff22,#6aa3ff05 60%), linear-gradient(180deg,#86f4ff08,#00000000);
  }

  .hud {
    display:flex;gap:10px;align-items:center;justify-content:space-between;padding:8px 6px;
    font-weight:700;color:#dfefff;font-size:15px;
  }

  .overlay {
    position:absolute;inset:0;display:flex;align-items:center;justify-content:center;
    background:linear-gradient(180deg, rgba(2,6,23,0.6), rgba(2,6,23,0.85));
    color:white;z-index:40;backdrop-filter:blur(3px);flex-direction:column;gap:10px;padding:20px;
    visibility:hidden;opacity:0;transition:opacity .18s ease, visibility .18s;
  }
  .overlay.active{visibility:visible;opacity:1}
  .overlay h2{margin:0;font-size:28px}
  .overlay p{margin:0;color:#cfe3ffaa}
  .overlay .row{display:flex;gap:10px;margin-top:12px}

  /* mobile touch controls */
  .touch-controls{
    position:absolute;bottom:14px;left:12px;display:flex;gap:12px;z-index:50;
  }
  .touch-button{
    width:56px;height:56px;border-radius:10px;background:rgba(0,0,0,0.35);display:flex;align-items:center;justify-content:center;
    color:white;font-weight:800;border:1px solid rgba(255,255,255,0.06);touch-action:none;
  }
  .touch-button.big{width:72px;height:72px;border-radius:14px}
  .mini{
    font-size:12px;color:#e8f2ffcc;margin-top:8px;text-align:center
  }

  @media (max-width:900px){
    .app{grid-template-columns:1fr;max-width:720px}
    canvas{height:420px}
  }
</style>
</head>
<body>
<div class="app" role="application" aria-label="Super Pixel Jump">
  <div class="left">
    <h1>Super Pixel Jump</h1>
    <div class="meta">Plataformas 2D — estilo clásico — controles precisos y físicas agradables.</div>

    <div class="scoreboard">
      <div class="stat"><div class="label">Puntuación</div><div id="uiScore" class="value">0</div></div>
      <div class="stat"><div class="label">Vidas</div><div id="uiLives" class="value">3</div></div>
    </div>

    <div style="margin-top:10px;font-size:13px;line-height:1.5">
      Recolecta monedas, evita o salta sobre los enemigos, y alcanza la bandera para completar el nivel.
      <div class="mini">Controles: ← → para moverte, ↑ o Espacio para saltar, R reinicia</div>
    </div>

    <div style="display:flex;gap:8px;margin-top:12px">
      <button class="btn" id="btnRestart">🔁 Reiniciar</button>
      <button class="btn" id="btnNext">⏭️ Siguiente</button>
    </div>

    <div class="controls">
      <p style="margin:0"><strong>Consejos</strong></p>
      <ul style="margin:8px 0 0 18px;font-size:13px;color:#eaf4ffcc">
        <li>Sostén el botón de salto para un salto ligeramente más alto.</li>
        <li>Pisa a los enemigos cuando caigas sobre ellos para eliminarlos.</li>
        <li>Las plataformas móviles empujan al jugador si está encima.</li>
      </ul>
    </div>

    <div style="font-size:12px;color:#f0f8ff88;margin-top:auto">
      Hecho con ❤️ — Código abierto — Usa para aprender o ampliar.
    </div>
  </div>

  <div class="right">
    <div class="canvas-wrap" id="gameWrap">
      <div class="hud">
        <div>Nivel: <span id="uiLevel">1</span></div>
        <div>Monedas: <span id="uiCoins">0</span></div>
      </div>
      <canvas id="gameCanvas" width="1000" height="560" aria-label="Canvas de juego"></canvas>

      <div class="overlay" id="overlay">
        <h2 id="overlayTitle">Paused</h2>
        <p id="overlayText">Presiona jugar para continuar</p>
        <div class="row">
          <button class="btn" id="overlayResume">Reanudar</button>
          <button class="btn" id="overlayRestart">Reiniciar</button>
        </div>
      </div>

      <!-- Touch controls for mobile -->
      <div class="touch-controls" id="touchControls" aria-hidden="true">
        <div class="touch-button" id="leftTouch">◀</div>
        <div style="display:flex;flex-direction:column;gap:8px">
          <div class="touch-button big" id="jumpTouch">▲</div>
          <div class="touch-button" id="rightTouch">▶</div>
        </div>
      </div>
    </div>

    <div style="display:flex;gap:10px;align-items:center;justify-content:space-between">
      <div style="font-size:13px;color:#cfe6ff">Estado del juego: <span id="gameState">Jugando</span></div>
      <div style="display:flex;gap:8px">
        <button class="btn" id="btnMute">🔊 Sonidos</button>
        <button class="btn" id="btnHelp">❓ Ayuda</button>
      </div>
    </div>
  </div>
</div>

<script>
/* ========== SUPER PIXEL JUMP - Juego 2D estilo Mario (original) ========== */

/* -----------------------------
   Configuración y utilidades
   ----------------------------- */
const canvas = document.getElementById('gameCanvas');
const ctx = canvas.getContext('2d', { alpha: false });
let canvasWidth = canvas.width;
let canvasHeight = canvas.height;

function clamp(v, a, b){ return Math.max(a, Math.min(b, v)); }
function rectsOverlap(a,b){
  return !(a.x+a.w <= b.x || a.x >= b.x+b.w || a.y+a.h <= b.y || a.y >= b.y+b.h);
}

/* -----------------------------
   Sonidos simples con WebAudio
   ----------------------------- */
let audioCtx = null;
let mute = false;
function ensureAudio(){
  if(!audioCtx) audioCtx = new (window.AudioContext || window.webkitAudioContext)();
}
function beep(freq, time=0.12, type='sine', vol=0.08){
  if(mute) return;
  ensureAudio();
  const o = audioCtx.createOscillator();
  const g = audioCtx.createGain();
  o.type = type; o.frequency.value = freq;
  g.gain.value = vol;
  o.connect(g); g.connect(audioCtx.destination);
  o.start();
  g.gain.exponentialRampToValueAtTime(0.0001, audioCtx.currentTime + time);
  setTimeout(()=>o.stop(), time*1000 + 20);
}
function coinSound(){ beep(880,0.12,'sine',0.08); }
function jumpSound(){ beep(420,0.12,'square',0.12); }
function stompSound(){ beep(220,0.14,'triangle',0.10); }
function dieSound(){ beep(120,0.4,'sawtooth',0.14); }

/* -----------------------------
   Nivel (mosaico)
   - Cada tile es 48px
   ----------------------------- */
const TILE = 48;
const GRAVITY = 1900; // px/s^2
const FRICTION_GROUND = 0.9;

const levels = [
`                                  G
                                  #
   ======          ===            #
                   #######        #
 P     C   C  CCC     E    ===    #
#############################  #####
#############################  #####`, // Nivel 1 (multiline string)
`                                 G
       C C   C    C  C     C
 P     ===      E     ====     #
      #######         ####    #
###############################`
];

// parse level to grid of tiles (right-trim)
function parseLevel(text){
  const rows = text.split('\\n').map(r => r.replace(/\\r/g,''));
  const height = rows.length;
  const width = Math.max(...rows.map(r => r.length));
  const grid = [];
  for(let y=0;y<height;y++){
    grid[y]=[];
    for(let x=0;x<width;x++){
      grid[y][x] = rows[y][x] || ' ';
    }
  }
  return {grid,width,height};
}

/* -----------------------------
   Entity classes: Player, Enemy, Coin, Platform
   ----------------------------- */
class Entity {
  constructor(x,y,w,h){ this.x=x; this.y=y; this.w=w; this.h=h; this.dead=false; }
  draw(){} update(dt){}
}

class Player extends Entity {
  constructor(x,y){
    super(x,y,34,42); // slightly smaller than tile for nicer collisions
    this.vx = 0; this.vy = 0;
    this.speed = 360;
    this.jumpPower = 620;
    this.onGround = false;
    this.facing = 1; // 1 right -1 left
    this.canDoubleJump = false;
    this.invulnerable = 0;
    this.frameTime = 0;
    this.frame = 0;
  }
  update(dt, world){
    // horizontal input handled externally
    this.vy += GRAVITY * dt;
    this.x += this.vx * dt;
    this.y += this.vy * dt;

    // collisions with solid tiles
    // compute AABB and clamp per-axis (simple tile-based)
    this.onGround = false;
    const solids = world.getSolidsInRect(this.x-2, this.y-2, this.w+4, this.h+4);
    for(const s of solids){
      if(rectsOverlap(this, s)){
        // compute overlap
        const ox = (this.x + this.w/2) - (s.x + s.w/2);
        const oy = (this.y + this.h/2) - (s.y + s.h/2);
        const px = (this.w/2 + s.w/2) - Math.abs(ox);
        const py = (this.h/2 + s.h/2) - Math.abs(oy);
        if(px < py){
          // resolve X
          if(ox > 0) this.x += px; else this.x -= px;
          this.vx = 0;
        } else {
          // resolve Y
          if(oy > 0){
            this.y += py; this.vy = 0;
          } else {
            this.y -= py; this.vy = 0; this.onGround = true; this.canDoubleJump = true;
          }
        }
      }
    }

    // Keep inside world bounds vertically (fall death)
    if(this.y > world.height* TILE + 300){
      this.die(world);
    }

    if(this.invulnerable > 0) this.invulnerable -= dt;

    // animation frames
    this.frameTime += dt;
    if(this.frameTime > 0.12){
      this.frame = (this.frame + 1) % 4;
      this.frameTime = 0;
    }
  }

  die(world){
    dieSound();
    world.lives--;
    if(world.lives <= 0){
      world.gameOver = true;
    } else {
      // respawn at start
      this.x = world.startX; this.y = world.startY;
      this.vx = this.vy = 0;
      this.invulnerable = 1.2;
    }
  }

  draw(ctx, camera){
    const sx = this.x - camera.x;
    const sy = this.y - camera.y;
    // draw a pixelated character: body, head, eyes
    ctx.save();
    ctx.translate(Math.round(sx), Math.round(sy));
    // flicker when invulnerable
    if(this.invulnerable>0 && Math.floor(this.invulnerable*10)%2===0) ctx.globalAlpha = 0.4;

    // shadow
    ctx.fillStyle = "rgba(2,8,22,0.25)";
    ctx.fillRect(6, this.h-6, this.w-12, 6);

    // body
    ctx.fillStyle = "#ff7a5c";
    ctx.fillRect(2, 8, this.w-4, this.h-16);

    // bib/overalls
    ctx.fillStyle = "#263cff";
    ctx.fillRect(2, this.h-28, this.w-4, 12);

    // head
    ctx.fillStyle = "#ffd7b3";
    ctx.fillRect(6, 0, this.w-12, 10);

    // eyes
    ctx.fillStyle = "#000";
    ctx.fillRect(10,4,3,3);
    ctx.fillRect(22,4,3,3);

    // legs animation
    ctx.fillStyle = "#231f20";
    const legOffset = (this.frame%2===0)?1:4;
    ctx.fillRect(8, this.h-8+legOffset, 6, 6);
    ctx.fillRect(this.w-14, this.h-8-legOffset, 6, 6);

    ctx.restore();
  }
}

class Enemy extends Entity {
  constructor(x,y, w=40, h=36){
    super(x,y,w,h);
    this.vx = 80;
    this.patrol = 120;
    this.baseX = x;
    this.dead = false;
    this.stomped = false;
    this.aliveTime = 0;
  }
  update(dt, world){
    if(this.dead) return;
    this.aliveTime += dt;
    // simple back and forth
    this.x += this.vx * dt;
    if(Math.abs(this.x - this.baseX) > this.patrol) this.vx *= -1;
    // gravity
    this.vy = (this.vy || 0) + GRAVITY * dt;
    this.y += this.vy*dt;
    // collisions with world ground
    const solids = world.getSolidsInRect(this.x-2, this.y-2, this.w+4, this.h+4);
    for(const s of solids){
      if(rectsOverlap(this,s)){
        const oy = (this.y + this.h/2) - (s.y + s.h/2);
        const py = (this.h/2 + s.h/2) - Math.abs(oy);
        if(py > 0){
          if(oy > 0) this.y += py, this.vy = 0;
          else this.y -= py, this.vy = 0;
        }
      }
    }
  }
  draw(ctx, camera){
    if(this.dead) return;
    const sx=this.x-camera.x, sy=this.y-camera.y;
    ctx.save();
    ctx.translate(Math.round(sx), Math.round(sy));
    // body
    ctx.fillStyle = "#7b2d2d";
    ctx.fillRect(2, 6, this.w-4, this.h-12);
    // shell / head
    ctx.fillStyle = "#8f3b3b";
    ctx.fillRect(6, 0, this.w-12, 10);
    // eyes
    ctx.fillStyle="#000";
    ctx.fillRect(this.w-14,4,3,3);
    ctx.fillRect(8,4,3,3);
    ctx.restore();
  }
}

class Coin extends Entity {
  constructor(x,y){
    super(x,y,28,28);
    this.collected=false;
    this.time=0;
  }
  update(dt){
    this.time += dt*8;
  }
  draw(ctx,camera){
    if(this.collected) return;
    const sx = this.x - camera.x, sy = this.y - camera.y;
    ctx.save(); ctx.translate(Math.round(sx), Math.round(sy));
    const r = 10 + Math.sin(this.time)*2;
    ctx.beginPath();
    ctx.fillStyle = "gold";
    ctx.ellipse(this.w/2, this.h/2, r, r*0.88, 0, 0, Math.PI*2);
    ctx.fill();
    ctx.fillStyle = "#fff7c6";
    ctx.fillRect(this.w/2-2, this.h/2-4, 4,8);
    ctx.restore();
  }
}

/* -----------------------------
   World: tiles, entities, camera
   ----------------------------- */
class World {
  constructor(levelIndex=0){
    this.levelIndex = levelIndex;
    this.load(levelIndex);
  }
  load(index){
    this.map = parseLevel(levels[index]);
    this.width = this.map.width;
    this.height = this.map.height;
    this.tiles = this.map.grid;
    this.entities = [];
    this.coins = [];
    this.enemies = [];
    this.platforms = [];
    this.startX = 64; this.startY = 64;
    this.score = 0;
    this.coinsCount = 0;
    this.lives = 3;
    this.gameOver = false;
    this.win = false;

    // parse tiles to create solids/entities
    for(let y=0;y<this.height;y++){
      for(let x=0;x<this.width;x++){
        const ch = this.tiles[y][x];
        const px = x*TILE, py = y*TILE;
        if(ch === '#'){ // ground (solid)
          // we'll use tile map for solids
        } else if(ch === '='){ // platform (thin)
          // treat as thin solid (platform)
        } else if(ch === 'P'){ // player
          this.startX = px+6; this.startY = py+TILE-50;
          this.tiles[y][x] = ' ';
        } else if(ch === 'C'){ // coin
          const coin = new Coin(px + (TILE-28)/2, py + (TILE-28)/2);
          this.coins.push(coin);
          this.coinsCount++;
          this.tiles[y][x] = ' ';
        } else if(ch === 'E'){ // enemy
          const enemy = new Enemy(px+4, py+TILE-36);
          this.enemies.push(enemy);
          this.tiles[y][x] = ' ';
        } else if(ch === 'G'){ // goal flag
          this.goal = {x:px, y:py, w:TILE, h:TILE};
          this.tiles[y][x] = ' ';
        }
      }
    }

    // Build player
    this.player = new Player(this.startX, this.startY);
    // camera
    this.camera = {x:0,y:0,w:canvasWidth,h:canvasHeight};
  }

  getSolidsInRect(x,y,w,h){
    // return array of solid rects (# and =) around area
    const rx1 = Math.floor(x / TILE) - 1;
    const ry1 = Math.floor(y / TILE) - 1;
    const rx2 = Math.floor((x+w) / TILE) + 1;
    const ry2 = Math.floor((y+h) / TILE) + 1;
    const solids = [];
    for(let j=ry1;j<=ry2;j++){
      for(let i=rx1;i<=rx2;i++){
        if(j<0 || i<0 || j>=this.height || i>=this.width) continue;
        const ch = this.tiles[j][i];
        if(ch === '#' || ch === '='){
          // For '=' make it thinner to act like platform
          if(ch === '#') solids.push({x:i*TILE, y:j*TILE, w:TILE, h:TILE});
          else solids.push({x:i*TILE, y:j*TILE + TILE*0.5, w:TILE, h:TILE*0.5});
        }
      }
    }
    return solids;
  }
}

/* -----------------------------
   Game state
   ----------------------------- */
let world = new World(0);
let lastTime = 0;
let keys = {};
let leftDown = false, rightDown = false, jumpDown = false;
let touchActive = false;

/* -----------------------------
   Input
   ----------------------------- */
document.addEventListener('keydown', e=>{
  if(e.key === 'ArrowLeft' || e.key==='a') leftDown = true;
  if(e.key === 'ArrowRight' || e.key==='d') rightDown = true;
  if(e.key === 'ArrowUp' || e.key === 'w' || e.code === 'Space') {
    if(!jumpDown){ // edge triggered
      handleJump();
    }
    jumpDown = true;
  }
  if(e.key === 'r'){ restartLevel(); }
  if(e.key === 'm'){ toggleMute(); }
});
document.addEventListener('keyup', e=>{
  if(e.key === 'ArrowLeft' || e.key==='a') leftDown = false;
  if(e.key === 'ArrowRight' || e.key==='d') rightDown = false;
  if(e.key === 'ArrowUp' || e.key === 'w' || e.code === 'Space') jumpDown = false;
});

function handleJump(){
  // perform jump based on player state
  const p = world.player;
  if(p.onGround){
    p.vy = -p.jumpPower;
    p.onGround = false;
    p.canDoubleJump = true;
    jumpSound();
  } else if(p.canDoubleJump){
    // allow one double jump (optional)
    p.vy = -p.jumpPower*0.9;
    p.canDoubleJump = false;
    jumpSound();
  }
}

/* Touch controls */
const leftTouch = document.getElementById('leftTouch');
const rightTouch = document.getElementById('rightTouch');
const jumpTouch = document.getElementById('jumpTouch');
[leftTouch, rightTouch, jumpTouch].forEach(el=>{
  el.addEventListener('touchstart', e=>{ e.preventDefault(); e.stopPropagation(); if(el===leftTouch) leftDown=true; if(el===rightTouch) rightDown=true; if(el===jumpTouch){ handleJump(); jumpDown=true;} });
  el.addEventListener('touchend', e=>{ e.preventDefault(); e.stopPropagation(); if(el===leftTouch) leftDown=false; if(el===rightTouch) rightDown=false; if(el===jumpTouch) jumpDown=false; });
});

/* -----------------------------
   Game loop
   ----------------------------- */
function update(dt){
  if(world.gameOver || world.win) return;

  // horizontal input smoothing
  const p = world.player;
  const target = (rightDown?1:0) - (leftDown?1:0);
  const accel = 2800;
  p.vx += (target * p.speed - p.vx) * clamp(dt*10, 0, 1);

  // update player and entities
  p.update(dt, world);

  // enemies
  for(const e of world.enemies) e.update(dt, world);

  // coins collection
  for(const c of world.coins){
    if(!c.collected && rectsOverlap(p, c)){
      c.collected = true;
      world.score += 100;
      world.coinsCount--;
      coinSound();
      document.getElementById('uiScore').textContent = world.score;
      document.getElementById('uiCoins').textContent = world.coinsCount;
    }
  }

  // enemy collisions
  for(const en of world.enemies){
    if(en.dead) continue;
    if(rectsOverlap(p, en)){
      // if player's downward velocity and above enemy -> stomp
      if(p.vy > 80 && (p.y + p.h - en.y) < 24){
        // stomp enemy
        stompSound();
        en.dead = true;
        world.score += 250;
        p.vy = -280; // bounce
        document.getElementById('uiScore').textContent = world.score;
      } else {
        // take damage if not invulnerable
        if(p.invulnerable <= 0){
          p.invulnerable = 1.2;
          p.x -= 40 * Math.sign(en.vx || 1);
          p.vy = -220;
          world.lives--;
          dieSound();
          document.getElementById('uiLives').textContent = world.lives;
          if(world.lives <= 0){
            world.gameOver = true;
            showOverlay("Game Over", "Has perdido todas tus vidas.");
          }
        }
      }
    }
  }

  // goal check
  if(world.goal){
    const flagRect = {x:world.goal.x, y:world.goal.y, w:world.goal.w, h:world.goal.h};
    if(rectsOverlap(p, flagRect)){
      world.win = true;
      playWin();
      showOverlay("¡Nivel Completado!", "Has llegado a la bandera. Siguiente nivel...");
    }
  }

  // camera follow
  const cam = world.camera;
  const centerX = p.x + p.w/2 - cam.w/2;
  const centerY = p.y + p.h/2 - cam.h/2;
  cam.x = clamp(centerX, 0, world.width*TILE - cam.w);
  cam.y = clamp(centerY, 0, world.height*TILE - cam.h);
}

/* -----------------------------
   Rendering
   ----------------------------- */
function clearScreen(){
  ctx.fillStyle = "#87ceeb22";
  ctx.fillRect(0,0,canvasWidth,canvasHeight);
}

function drawGrid(world){
  const cam = world.camera;
  // draw parallax background simple
  ctx.fillStyle = "#8ad2ff22";
  ctx.fillRect(0,0,canvasWidth, 120);

  // draw tiles
  for(let y=0;y<world.height;y++){
    for(let x=0;x<world.width;x++){
      const ch = world.tiles[y][x];
      const px = x*TILE - cam.x;
      const py = y*TILE - cam.y;
      if(px + TILE < 0 || px > canvasWidth || py + TILE < 0 || py > canvasHeight) continue;
      if(ch === '#'){
        // ground block - draw as filled pixel tile
        ctx.fillStyle = "#6b4a2b";
        ctx.fillRect(px, py, TILE, TILE);
        // highlight
        ctx.fillStyle = "#8b5a33";
        ctx.fillRect(px+6, py+6, TILE-12, TILE-12);
      } else if(ch === '='){
        // platform (thin)
        ctx.fillStyle = "#b48e5b";
        ctx.fillRect(px, py + TILE*0.5, TILE, TILE*0.5);
      }
    }
  }
  // draw goal flag
  if(world.goal){
    const gx = world.goal.x - cam.x;
    const gy = world.goal.y - cam.y;
    ctx.fillStyle = "#552b8a";
    ctx.fillRect(gx + TILE-6, gy, 6, TILE);
    ctx.fillStyle = "#ff2f6a";
    ctx.beginPath();
    ctx.moveTo(gx + TILE-6, gy+6);
    ctx.lineTo(gx + TILE-6 + 20, gy+14);
    ctx.lineTo(gx + TILE-6, gy + 24);
    ctx.fill();
  }
}

function drawEntities(world){
  const cam = world.camera;
  // coins
  for(const c of world.coins) c.draw(ctx, cam);
  // enemies
  for(const e of world.enemies) e.draw(ctx, cam);
  // player
  world.player.draw(ctx, cam);
}

function render(){
  clearScreen();
  drawGrid(world);
  drawEntities(world);
  // overlays like score, etc already in HUD
}

/* -----------------------------
   Game loop control
   ----------------------------- */
function loop(ts){
  if(!lastTime) lastTime = ts;
  const dt = Math.min(0.033, (ts - lastTime) / 1000);
  lastTime = ts;

  update(dt);
  render();

  requestAnimationFrame(loop);
}

/* -----------------------------
   UI helpers and controls
   ----------------------------- */
function restartLevel(){
  world.load(world.levelIndex);
  document.getElementById('uiScore').textContent = world.score;
  document.getElementById('uiLives').textContent = world.lives;
  document.getElementById('uiCoins').textContent = world.coinsCount;
  document.getElementById('uiLevel').textContent = world.levelIndex + 1;
  world.gameOver = false;
  world.win = false;
  // Asegurar que la última fila sea todo suelo sólido
for(let x=0;x<this.width;x++){
    this.tiles[this.height-1][x] = '#';
}

  hideOverlay();
}
document.getElementById('btnRestart').addEventListener('click', restartLevel);
document.getElementById('btnNext').addEventListener('click', ()=>{
  world.levelIndex = (world.levelIndex + 1) % levels.length;
  restartLevel();
});

function showOverlay(title, text){
  const ov = document.getElementById('overlay');
  document.getElementById('overlayTitle').textContent = title;
  document.getElementById('overlayText').textContent = text;
  ov.classList.add('active');
  document.getElementById('gameState').textContent = title;
}

function hideOverlay(){
  const ov = document.getElementById('overlay');
  ov.classList.remove('active');
  document.getElementById('gameState').textContent = 'Jugando';
}
document.getElementById('overlayResume').addEventListener('click', hideOverlay);
document.getElementById('overlayRestart').addEventListener('click', restartLevel);

function toggleMute(){
  mute = !mute;
  document.getElementById('btnMute').textContent = mute ? '🔈 Silencio' : '🔊 Sonidos';
  if(!mute) ensureAudio();
}
document.getElementById('btnMute').addEventListener('click', toggleMute);

document.getElementById('btnHelp').addEventListener('click', ()=>{
  showOverlay("Ayuda", "Controles: ← → moverse, ↑/Espacio saltar, R reiniciar.\nSalta encima de los enemigos para eliminarlos. Recolecta monedas para puntos.");
});

/* -----------------------------
   Level progression and win sound
   ----------------------------- */
function playWin(){
  if(mute) return;
  ensureAudio();
  const now = audioCtx.currentTime;
  const freqs = [440,660,880,1320];
  freqs.forEach((f,i)=>{
    const o = audioCtx.createOscillator();
    const g = audioCtx.createGain();
    o.frequency.value = f;
    o.type = 'sine';
    g.gain.value = 0.08;
    o.connect(g); g.connect(audioCtx.destination);
    o.start(now + i*0.06);
    g.gain.exponentialRampToValueAtTime(0.0001, now + i*0.06 + 0.12);
    setTimeout(()=>o.stop(), (i*0.06 + 0.14)*1000);
  });
}

/* -----------------------------
   Resize handling
   ----------------------------- */
function fitCanvas(){
  // canvas element uses CSS sizing; make internal resolution match for crispness
  const rect = canvas.getBoundingClientRect();
  canvasWidth = Math.floor(rect.width);
  canvasHeight = Math.floor(rect.height);
  canvas.width = canvasWidth * (window.devicePixelRatio || 1);
  canvas.height = canvasHeight * (window.devicePixelRatio || 1);
  ctx.setTransform(window.devicePixelRatio || 1,0,0,window.devicePixelRatio || 1,0,0);
  world.camera.w = canvasWidth; world.camera.h = canvasHeight;
}
window.addEventListener('resize', ()=>{ fitCanvas(); });
fitCanvas();

/* -----------------------------
   Start game
   ----------------------------- */
restartLevel();
requestAnimationFrame(loop);

/* -----------------------------
   Final: expose small API to page
   ----------------------------- */
document.getElementById('uiScore').textContent = world.score;
document.getElementById('uiLives').textContent = world.lives;
document.getElementById('uiCoins').textContent = world.coinsCount;
document.getElementById('uiLevel').textContent = world.levelIndex + 1;

/* Make controls work on desktop (smooth) */
(function inputTick(){
  // Update UI states for pause / overlays
  if(world.gameOver){
    showOverlay("Game Over", "Has perdido todas tus vidas. Reinicia para intentar de nuevo.");
  }
  // throttle stats
  document.getElementById('uiScore').textContent = world.score;
  document.getElementById('uiCoins').textContent = world.coinsCount;
  document.getElementById('uiLives').textContent = world.lives;
  document.getElementById('uiLevel').textContent = world.levelIndex + 1;
  setTimeout(inputTick, 120);
})();
</script>
</body>
</html>
