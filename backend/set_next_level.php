<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');

require "./include/db_mysqli.php";

// Parámetros por GET o POST (sin sanitizar, para vulnerabilidad)
$usuari_id = isset($_REQUEST['usuari_id']) ? (int)$_REQUEST['usuari_id'] : 0;
$joc_id = isset($_REQUEST['joc_id']) ? (int)$_REQUEST['joc_id'] : 0;
$nou_nivell = isset($_REQUEST['nivell']) ? (int)$_REQUEST['nivell'] : 1;

// Actualizar a siguiente nivel. La puntuación máxima se pone a 0!!
$sql = "UPDATE progres_usuari 
        SET nivell_actual = $nou_nivell, puntuacio_maxima = 0 
        WHERE usuari_id = $usuari_id AND joc_id = $joc_id";

$result = $conn->query($sql);

if ($result) {
    echo json_encode([
        "status" => "ok",
        "message" => "Nivel actualizado",
        "update_sql" => $sql
    ]);
} else {
    echo json_encode([
        "status" => "error",
        "message" => $conn->error,
        "update_sql" => $sql
    ]);
}

$conn->close();
?>
