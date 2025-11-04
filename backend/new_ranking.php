<?php
session_start();
require "./include/db_mysqli.php";

if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Consulta del ranking global (sin sanitizar, vulnerable a propósito)
$sql = "
SELECT 
    u.nom_usuari,
    SUM(p.nivell_actual * 100 + p.puntuacio_maxima) AS puntuacio_final_total,
    COUNT(DISTINCT p.joc_id) AS jocs_jugats
FROM progres_usuari p
JOIN usuaris u ON p.usuari_id = u.id
GROUP BY u.nom_usuari
ORDER BY puntuacio_final_total DESC;
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking General - NovaPlay</title>
    <style>
        :root {
            --bg: #0F172A;
            --bg-grad: radial-gradient(circle at top right, rgba(124, 58, 237, 0.25), transparent 60%);
            --card-bg: #1E293B;
            --text-light: #F1F5F9;
            --text-muted: #E2E8F0;
            --accent-from: #A855F7;
            --accent-to: #7C3AED;
            --button: #8B5CF6;
            --highlight: #10B981;
            --pink: #D946EF;
        }

        body {
            margin: 0;
            font-family: "Inter", system-ui, sans-serif;
            background: var(--bg);
            background-image: var(--bg-grad);
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 40px;
        }

        h1 {
            font-size: 28px;
            background: linear-gradient(90deg, var(--accent-from), var(--accent-to));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 30px;
            text-align: center;
        }

        .ranking-card {
            width: 90%;
            max-width: 800px;
            background: var(--card-bg);
            border-radius: 22px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.45);
            border: 1px solid rgba(255,255,255,0.05);
            overflow: hidden;
            padding: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: linear-gradient(90deg, var(--accent-from), var(--accent-to));
            color: white;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 12px 15px;
        }

        td {
            text-align: center;
            padding: 12px 15px;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            color: var(--text-light);
        }

        tr:nth-child(even) {
            background-color: rgba(255, 255, 255, 0.03);
        }

        tr:hover {
            background: rgba(124, 58, 237, 0.1);
        }

        .highlight {
            color: var(--highlight);
            font-weight: 700;
        }

        .footer {
            margin-top: 25px;
            text-align: center;
        }

        .link {
            color: var(--pink);
            text-decoration: none;
            font-weight: 600;
        }

        .link:hover {
            text-decoration: underline;
        }

        .logo {
            font-size: 50px;
            margin-bottom: 10px;
        }

        .user-row {
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="logo">🏆</div>
    <h1>Ranking General de Jugadores</h1>

    <div class="ranking-card">
        <table>
            <tr>
                <th>Posición</th>
                <th>Usuario</th>
                <th>Jocs Jugats</th>
                <th>Puntuación Final</th>
            </tr>

            <?php
            if ($result && $result->num_rows > 0) {
                $pos = 1;
                while($row = $result->fetch_assoc()) {
                    echo "<tr class='user-row'>";
                    echo "<td>" . $pos++ . "</td>";
                    echo "<td>" . htmlspecialchars($row['nom_usuari']) . "</td>";
                    echo "<td>" . $row['jocs_jugats'] . "</td>";
                    echo "<td class='highlight'>" . $row['puntuacio_final_total'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No hi ha dades disponibles</td></tr>";
            }
            ?>
        </table>
    </div>

    <div class="footer">
        <a href="plataforma.php" class="link">⬅ Tornar a la plataforma</a>
    </div>

</body>
</html>

<?php
$conn->close();
?>
