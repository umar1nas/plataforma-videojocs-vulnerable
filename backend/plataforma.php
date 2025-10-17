<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php'); // o donde esté el login
    exit;
}

$nombreUsuario = ucfirst($_SESSION['usuario']); // 🔹 Convierte la primera letra en mayúscula

$usuario = [
    'nombre' => $nombreUsuario,
    'avatar' => '👤'
];

$juegos = [
    ['id' => 1, 'nombre' => 'Joc Complet', 'emoji' => '🧩', 'url' => 'joc/1/index.html'],
    ['id' => 2, 'nombre' => 'Pong', 'emoji' => '🧠', 'url' => 'joc/2/pong.php'],
    ['id' => 3, 'nombre' => 'Trivia Quest', 'emoji' => '🎯', 'url' => 'juegos/trivia.php'],
    ['id' => 4, 'nombre' => 'Snake Attack', 'emoji' => '🐍', 'url' => 'joc/3/snake.php'],
    ['id' => 5, 'nombre' => 'Flappy Bird', 'emoji' => '🐦', 'url' => 'juegos/flappy.php'],
    ['id' => 6, 'nombre' => '2048', 'emoji' => '🎮', 'url' => 'juegos/2048.php']
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Game Portal - Centro de Juegos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <!-- BOTÓN RANKING (IZQUIERDA) -->
        <aside class="ranking-sidebar">
            <a href="ranking.php" class="ranking-btn" title="Ver Ranking Completo">
                🏆
            </a>
        </aside>

        <!-- BOTÓN PERFIL (ARRIBA DERECHA) -->
        <header class="top-bar">
            <div class="logo">
                <h1>🎮 Game Portal</h1>
            </div>
            <button class="profile-btn" onclick="toggleProfile()">
                <?php echo $usuario['avatar']; ?> <?php echo htmlspecialchars($usuario['nombre']); ?>
            </button>
            
            <!-- Panel del Perfil (oculto por defecto) -->
            <div id="profilePanel" class="profile-panel">
                <div class="profile-header">
                    <span class="profile-avatar"><?php echo $usuario['avatar']; ?></span>
                    <h3><?php echo htmlspecialchars($usuario['nombre']); ?></h3>
                </div>
                <div class="profile-options">
                    <a href="perfil.php" class="profile-option">⚙️ Perfil</a>
                    <a href="logout.php" class="profile-option logout">🚪 Cerrar Sesión</a>
                </div>
            </div>
        </header>

        <!-- CONTENIDO PRINCIPAL -->
        <main class="main-content">
            <section class="welcome-section">
                <h2>Bienvenido, <?php echo htmlspecialchars(explode(' ', $usuario['nombre'])[0]); ?>!</h2>
                <p>Elige un juego y comienza a jugar</p>
            </section>

            <!-- SLIDER DE JUEGOS -->
            <section class="games-section">
                <div class="slider-container">
                    <button class="slider-btn prev-btn" onclick="slideGames('prev')">❮</button>
                    
                    <div class="slider-wrapper">
                        <div class="slider" id="gamesSlider">
                            <?php foreach ($juegos as $juego): ?>
                                <div class="game-card">
                                    <div class="game-emoji"><?php echo $juego['emoji']; ?></div>
                                    <h3><?php echo htmlspecialchars($juego['nombre']); ?></h3>
                                    <a href="<?php echo htmlspecialchars($juego['url']); ?>" class="game-link">
                                        Jugar →
                                    </a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <button class="slider-btn next-btn" onclick="slideGames('next')">❯</button>
                </div>
            </section>
        </main>
    </div>

    <script>
        let currentSlide = 0;

        function slideGames(direction) {
            const slider = document.getElementById('gamesSlider');
            const cards = document.querySelectorAll('.game-card');
            const cardWidth = cards[0].offsetWidth + 20; // incluir gap

            if (direction === 'next') {
                currentSlide += cardWidth;
                if (currentSlide > cardWidth * (cards.length - 3)) {
                    currentSlide = 0;
                }
            } else {
                currentSlide -= cardWidth;
                if (currentSlide < 0) {
                    currentSlide = cardWidth * (cards.length - 3);
                }
            }

            slider.style.transform = `translateX(-${currentSlide}px)`;
        }

        function toggleRanking() {
            const panel = document.getElementById('rankingPanel');
            panel.classList.toggle('active');
            document.getElementById('profilePanel').classList.remove('active');
        }

        function toggleProfile() {
            const panel = document.getElementById('profilePanel');
            panel.classList.toggle('active');
            document.getElementById('rankingPanel').classList.remove('active');
        }

        // Cerrar paneles al hacer clic fuera
        document.addEventListener('click', function(event) {
            const rankingPanel = document.getElementById('rankingPanel');
            const profilePanel = document.getElementById('profilePanel');
            const rankingBtn = document.querySelector('.ranking-btn');
            const profileBtn = document.querySelector('.profile-btn');

            if (!rankingPanel.contains(event.target) && !rankingBtn.contains(event.target)) {
                rankingPanel.classList.remove('active');
            }
            if (!profilePanel.contains(event.target) && !profileBtn.contains(event.target)) {
                profilePanel.classList.remove('active');
            }
        });
    </script>
</body>
</html>