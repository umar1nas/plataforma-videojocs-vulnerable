<?php


session_start();
require "./include/db_mysqli.php";

if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

// Consulta del ranking global
$sql = "
    SELECT 
        u.nom_usuari,
        SUM(p.puntuacio_obtinguda) AS total_punts,
        COUNT(p.id) AS partides_jugades
    FROM partides p
    JOIN usuaris u ON p.usuari_id = u.id
    GROUP BY u.nom_usuari
    ORDER BY total_punts DESC;
";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ranking General</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f7f7f7;
            color: #333;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-top: 50px;
        }
        h1 {
            color: #222;
        }
        table {
            border-collapse: collapse;
            width: 60%;
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 20px;
            text-align: center;
        }
        th {
            background: #007BFF;
            color: white;
        }
        tr:nth-child(even) {
            background: #f2f2f2;
        }
        tr:hover {
            background: #e6f3ff;
        }
    </style>
</head>
<body>
    <h1>🏆 Ranking General de Jugadores</h1>
    <table>
        <tr>
            <th>Posición</th>
            <th>Usuario</th>
            <th>Puntos Totales</th>
            <th>Partidas Jugadas</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            $pos = 1;
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $pos++ . "</td>";
                echo "<td>" . htmlspecialchars($row['nom_usuari']) . "</td>";
                echo "<td>" . $row['total_punts'] . "</td>";
                echo "<td>" . $row['partides_jugades'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No hay datos disponibles</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php
$conn->close();
?>
