<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: ../../../index.php'); // o donde esté el login
    exit;
}

// ⚙️ Configuració inicial
$usuari_id = $_SESSION['usuari_id']; // ID del usuario en sesión
$joc_id = 1; // ID del juego
$nom_usuari = $_SESSION['usuario'];

require_once "../../include/db_mysqli.php";
require '../../../config.php';


// 🔍 Consultar el nivel actual del usuario
$sql_progres = "SELECT nivell_actual FROM progres_usuari 
                WHERE usuari_id = $usuari_id AND joc_id = $joc_id LIMIT 1";
$res_progres = $conn->query($sql_progres);

$nivell_actual = 1; // valor por defecto
if ($res_progres && $res_progres->num_rows > 0) {
  $row = $res_progres->fetch_assoc();
  $nivell_actual = (int)$row['nivell_actual'];
}
?>
<!DOCTYPE html>
<html lang="ca">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Joc interactiu HTML, CSS i JavaScript</title>
    <meta name="description" content="Joc JS per treballar la manipulació del DOM, la gestió d'esdeveniments i la POO." />
    <meta name="author" content="Xavi Garcia @xavig-icv" />
    <meta name="copyright" content="Xavi Garcia @xavig-icv" />
    <link rel="stylesheet" href="./css/index.css" />
  </head>

  <body>
    <div id="pantalla"></div>
    <div id="infoPartida"></div>

    <!-- 🔁 Aquí inyectamos los datos del backend a JavaScript -->
    <script>
      window.jocConfig = {
        jocId: <?php echo $joc_id; ?>,
        nivell: <?php echo $nivell_actual; ?>,
        nomUsuari: "<?php echo htmlspecialchars($nom_usuari, ENT_QUOTES); ?>",
        usuariId: <?php echo $usuari_id; ?>
      };
      window.baseIP = "<?= $BASE_IP ?>";
      console.log("Config joc:", window.jocConfig);
    </script>

    <!-- Scripts del joc -->
    <script src="./js/classes.js"></script>
    <script src="./js/main.js"></script>
  </body>
</html>