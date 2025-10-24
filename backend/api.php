<?php
// api_basica.php
require_once "./db_pdo.php";

// Definim el tipus de resposta per a tota l'API
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // VULNERABILITAT: CORS permissiu a tothom

// Obtenim la URL sol·licitada
$uri = explode("/", trim($_SERVER['REQUEST_URI'], "/"));

// VIGILEU el número d'elements de la vostra URI pot canviar!!
// Ex: /api.php/jocs/1/nivells/3
// uri[0] = api, uri[1] = jocs, uri[2] = 1, uri[3] = nivells, uri[4] = 3

$method = $_SERVER['REQUEST_METHOD'];

if ($method === "GET") {
  // Validar l'estructura: /api.php/jocs/{id}/nivells/{nivell}
  if (isset($_GET['jocs']) && isset($_GET['nivells'])) {

    $jocId = (int) $_GET['jocs'];
    $nivell = (int) $_GET['nivells'];

    // Exemple vulnerable a SQL Injection
    $sql = "SELECT configuracio_json FROM nivells_joc WHERE joc_id = $jocId AND nivell = $nivell";

    $resultat = $pdo->query($sql);

    if ($resultat && $fila = $resultat->fetch(PDO::FETCH_ASSOC)) {
        echo $fila['configuracio_json']; // Retornem directament el JSON guardat
    } else {
        http_response_code(404);
        echo json_encode(["error" => "Joc o nivell no trobat"]);
    }
  } else {
      http_response_code(400);
      echo json_encode(["error" => "Ruta no vàlida. Exemple: /api/jocs/1/nivells/3"]);
  }
} else {
    http_response_code(405); // Mètode no permès
    echo json_encode(["error" => "Mètode no suportat"]);
}

$pdo = null;
?>