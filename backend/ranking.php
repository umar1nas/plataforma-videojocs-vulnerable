<?php
// Base de datos de ranking (en producción vendría de una BD real)
$ranking_completo = [
    ['posicion' => 1, 'nombre' => 'Carlos López', 'puntos' => 5420, 'juegos_jugados' => 142, 'nivel' => '⭐⭐⭐'],
    ['posicion' => 2, 'nombre' => 'María García', 'puntos' => 4890, 'juegos_jugados' => 128, 'nivel' => '⭐⭐⭐'],
    ['posicion' => 3, 'nombre' => 'Juan Pérez', 'puntos' => 4320, 'juegos_jugados' => 115, 'nivel' => '⭐⭐'],
    ['posicion' => 4, 'nombre' => 'Ana Martínez', 'puntos' => 3950, 'juegos_jugados' => 98, 'nivel' => '⭐⭐'],
    ['posicion' => 5, 'nombre' => 'Pedro Ruiz', 'puntos' => 3420, 'juegos_jugados' => 87, 'nivel' => '⭐'],
    ['posicion' => 6, 'nombre' => 'Laura Jiménez', 'puntos' => 3150, 'juegos_jugados' => 76, 'nivel' => '⭐'],
    ['posicion' => 7, 'nombre' => 'David Moreno', 'puntos' => 2890, 'juegos_jugados' => 65, 'nivel' => '⭐'],
    ['posicion' => 8, 'nombre' => 'Elena Sánchez', 'puntos' => 2520, 'juegos_jugados' => 54, 'nivel' => '⭐'],
    ['posicion' => 9, 'nombre' => 'Roberto Franco', 'puntos' => 2150, 'juegos_jugados' => 43, 'nivel' => ''],
    ['posicion' => 10, 'nombre' => 'Sofia Delgado', 'puntos' => 1890, 'juegos_jugados' => 35, 'nivel' => '']
];

// Simulamos el usuario actual
$usuario_actual = 'Juan Pérez';
$posicion_usuario = 3;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏆 Ranking - Game Portal</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .ranking-page {
            min-height: 100vh;
            padding: 0;
            margin: 0;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        .ranking-header {
            background: linear-gradient(135deg, #6366f1, #ec4899);
            padding: 40px 20px;
            text-align: center;
            position: relative;
        }

        .ranking-header h1 {
            font-size: 48px;
            margin-bottom: 10px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }

        .ranking-header p {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.9);
        }

        .back-btn {
            position: absolute;
            left: 20px;
            top: 20px;
            background-color: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .back-btn:hover {
            background-color: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
            transform: translateX(-5px);
        }

        .ranking-container {
            max-width: 900px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .your-position {
            background: linear-gradient(135deg, #334155, #475569);
            border: 3px solid #6366f1;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 40px;
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        .your-position-title {
            font-size: 14px;
            color: #cbd5e1;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .your-position-content {
            display: flex;
            align-items: center;
            gap: 30px;
        }

        .your-position-rank {
            font-size: 64px;
            font-weight: bold;
            color: #6366f1;
            text-shadow: 0 2px 10px rgba(99, 102, 241, 0.5);
        }

        .your-position-info {
            flex: 1;
        }

        .your-position-name {
            font-size: 24px;
            font-weight: bold;
            color: #f1f5f9;
            margin-bottom: 10px;
        }

        .your-position-stats {
            display: flex;
            gap: 30px;
            font-size: 14px;
            color: #cbd5e1;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
        }

        .stat-label {
            color: #94a3b8;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .stat-value {
            color: #10b981;
            font-weight: bold;
            font-size: 16px;
        }

        .ranking-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }

        .ranking-table thead {
            background: linear-gradient(135deg, #1e293b, #334155);
            border-bottom: 2px solid #6366f1;
        }

        .ranking-table th {
            padding: 20px;
            text-align: left;
            font-weight: bold;
            color: #cbd5e1;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .ranking-table tbody tr {
            border-bottom: 1px solid #475569;
            transition: all 0.3s ease;
        }

        .ranking-table tbody tr:hover {
            background-color: #334155;
            box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
        }

        .ranking-table td {
            padding: 20px;
            color: #f1f5f9;
        }

        .rank-number {
            font-weight: bold;
            font-size: 20px;
            color: #6366f1;
            min-width: 40px;
        }

        .rank-number.top-1 {
            color: #fbbf24;
            font-size: 24px;
        }

        .rank-number.top-2 {
            color: #c0cfd9;
            font-size: 22px;
        }

        .rank-number.top-3 {
            color: #d97706;
            font-size: 22px;
        }

        .player-name {
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .player-medal {
            font-size: 20px;
        }

        .points {
            color: #10b981;
            font-weight: bold;
        }

        .level-badge {
            display: inline-block;
            padding: 4px 12px;
            background: linear-gradient(135deg, #6366f1, #ec4899);
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
        }

        .no-level {
            color: #94a3b8;
            font-style: italic;
        }

        .pagination {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 40px;
        }

        .pagination a, .pagination button {
            padding: 10px 15px;
            border: 2px solid #6366f1;
            background-color: transparent;
            color: #6366f1;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: bold;
        }

        .pagination a:hover, .pagination button:hover {
            background-color: #6366f1;
            color: white;
        }

        .pagination .active {
            background-color: #6366f1;
            color: white;
        }

        .footer {
            text-align: center;
            padding: 40px 20px;
            color: #94a3b8;
            border-top: 1px solid #475569;
            margin-top: 40px;
        }

        @media (max-width: 768px) {
            .ranking-header h1 {
                font-size: 36px;
            }

            .your-position-content {
                flex-direction: column;
                text-align: center;
            }

            .your-position-stats {
                justify-content: center;
                flex-wrap: wrap;
            }

            .ranking-table th,
            .ranking-table td {
                padding: 15px 10px;
                font-size: 14px;
            }

            .rank-number {
                min-width: 30px;
                font-size: 16px;
            }

            .player-medal {
                font-size: 16px;
            }
        }
    </style>
</head>
<body class="ranking-page">
    <a href="index.php" class="back-btn">← Volver</a>

    <div class="ranking-header">
        <h1>🏆 Ranking Global</h1>
        <p>Los mejores jugadores de Game Portal</p>
    </div>

    <div class="ranking-container">
        <!-- Tu posición actual -->
        <div class="your-position">
            <div class="your-position-title">Tu Posición Actual</div>
            <div class="your-position-content">
                <div class="your-position-rank">#<?php echo $posicion_usuario; ?></div>
                <div class="your-position-info">
                    <div class="your-position-name"><?php echo htmlspecialchars($usuario_actual); ?></div>
                    <div class="your-position-stats">
                        <div class="stat-item">
                            <span class="stat-label">Puntos</span>
                            <span class="stat-value"><?php echo number_format($ranking_completo[$posicion_usuario - 1]['puntos']); ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Juegos Jugados</span>
                            <span class="stat-value"><?php echo $ranking_completo[$posicion_usuario - 1]['juegos_jugados']; ?></span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-label">Nivel</span>
                            <span class="stat-value"><?php echo $ranking_completo[$posicion_usuario - 1]['nivel'] ?: 'Principiante'; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabla de ranking -->
        <table class="ranking-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Posición</th>
                    <th style="width: 40%;">Jugador</th>
                    <th style="width: 20%;">Puntos</th>
                    <th style="width: 15%;">Juegos</th>
                    <th style="width: 15%;">Nivel</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ranking_completo as $item): ?>
                    <tr>
                        <td>
                            <span class="rank-number <?php echo $item['posicion'] <= 3 ? 'top-' . $item['posicion'] : ''; ?>">
                                <?php 
                                    if ($item['posicion'] == 1) echo '🥇';
                                    elseif ($item['posicion'] == 2) echo '🥈';
                                    elseif ($item['posicion'] == 3) echo '🥉';
                                    else echo '#' . $item['posicion'];
                                ?>
                            </span>
                        </td>
                        <td>
                            <div class="player-name">
                                <?php echo htmlspecialchars($item['nombre']); ?>
                                <?php if ($item['nombre'] == $usuario_actual): ?>
                                    <span style="color: #6366f1; font-weight: bold;">(Tú)</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td><span class="points"><?php echo number_format($item['puntos']); ?></span></td>
                        <td><?php echo $item['juegos_jugados']; ?></td>
                        <td>
                            <?php if ($item['nivel']): ?>
                                <span class="level-badge"><?php echo $item['nivel']; ?></span>
                            <?php else: ?>
                                <span class="no-level">Principiante</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pie de página -->
        <div class="footer">
            <p>💡 El ranking se actualiza cada hora. ¡Sigue jugando para mejorar tu posición!</p>
        </div>
    </div>
</body>
</html>