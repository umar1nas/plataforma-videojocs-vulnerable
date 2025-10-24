<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

require "../../include/db_mysqli.php";

$usuari_id = isset($_REQUEST['usuari_id']) ? (int)$_REQUEST['usuari_id'] : 0;
$joc_id    = isset($_REQUEST['joc_id'])    ? (int)$_REQUEST['joc_id'] : 0;
$nivell    = isset($_REQUEST['nivell'])    ? (int)$_REQUEST['nivell'] : 0;
$puntuacio = isset($_REQUEST['puntuacio']) ? (int)$_REQUEST['puntuacio'] : 0;
$durada    = isset($_REQUEST['durada'])    ? (int)$_REQUEST['durada'] : 0;

$now = date('Y-m-d H:i:s');

// Guardar la partida siempre
$sql_insert = "INSERT INTO partides 
(usuari_id, joc_id, nivell_jugat, puntuacio_obtinguda, data_partida, durada_segons)
VALUES ($usuari_id, $joc_id, $nivell, $puntuacio, '$now', $durada)";
$result = $conn->query($sql_insert);

if (!$result) {
    echo json_encode([
        "status" => "error",
        "message" => "Error insert partides: " . $conn->error,
        "sql" => $sql_insert
    ]);
    exit;
}

// Verificar si existe progreso del usuario para este juego
$sql_check = "SELECT * FROM progres_usuari WHERE usuari_id = $usuari_id AND joc_id = $joc_id LIMIT 1";
$res_check = $conn->query($sql_check);

if ($res_check && $res_check->num_rows > 0) {
    $row = $res_check->fetch_assoc();
    $partides_jugades = (int)$row['partides_jugades'] + 1;

    $nivel_guardado = (int)$row['nivell_actual'];
    $puntuacion_guardada = (int)$row['puntuacio_maxima'];

    $new_nivell = $nivel_guardado;
    $new_puntuacio = $puntuacion_guardada;
    $update_needed = false;

    // Si el nuevo nivel es mayor → actualizar nivel y puntuación (sin comprobar nada más)
    if ($nivell > $nivel_guardado) {
        $new_nivell = $nivell;
        $new_puntuacio = $puntuacio;
        $update_needed = true;
    }
    // Si el nivel es el mismo → comparar puntuación
    elseif ($nivell == $nivel_guardado && $puntuacio > $puntuacion_guardada) {
        $new_puntuacio = $puntuacio;
        $update_needed = true;
    }

    if ($update_needed) {
        $sql_update = "UPDATE progres_usuari SET 
            nivell_actual = $new_nivell,
            puntuacio_maxima = $new_puntuacio,
            partides_jugades = $partides_jugades,
            ultima_partida = '$now'
            WHERE usuari_id = $usuari_id AND joc_id = $joc_id";
    } else {
        // Solo actualiza número de partidas y fecha si no hay mejora de nivel o puntuación
        $sql_update = "UPDATE progres_usuari SET 
            partides_jugades = $partides_jugades,
            ultima_partida = '$now'
            WHERE usuari_id = $usuari_id AND joc_id = $joc_id";
    }

    $res_update = $conn->query($sql_update);
    if (!$res_update) {
        echo json_encode([
            "status" => "error",
            "message" => "Error updating progreso: " . $conn->error,
            "sql" => $sql_update
        ]);
        exit;
    }

} else {
    // No existe progreso: crear uno nuevo
    $sql_insert_prog = "INSERT INTO progres_usuari 
    (usuari_id, joc_id, nivell_actual, puntuacio_maxima, partides_jugades, ultima_partida)
    VALUES ($usuari_id, $joc_id, $nivell, $puntuacio, 1, '$now')";

    $res_insert = $conn->query($sql_insert_prog);
    if (!$res_insert) {
        echo json_encode([
            "status" => "error",
            "message" => "Error inserting progreso: " . $conn->error,
            "sql" => $sql_insert_prog
        ]);
        exit;
    }
}

echo json_encode([
    "status" => "ok",
    "message" => "Partida guardada y progreso actualizado correctamente.",
    "insert_sql" => $sql_insert
]);
$conn->close();
?>