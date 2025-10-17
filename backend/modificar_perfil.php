<?php
session_start();
require "./include/db_mysqli.php";

if (!isset($_SESSION['usuario'])) {
    header('Location: ../index.php');
    exit;
}

$usuario_actual = $_SESSION['usuario'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tomamos valores tal cual (vulnerable a propósito)
    $nombre = isset($_POST['nombre']) ? $_POST['nombre'] : '';
    $usuario_nuevo = isset($_POST['usuario']) ? $_POST['usuario'] : '';
    $email = isset($_POST['email']) ? $_POST['email'] : '';

    // --- FOTO ---
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        // Si el usuario cambió su nombre en el formulario, usar ese nombre para el archivo,
        // si está vacío, usar el nombre actual de sesión.
        $base_nombre = $usuario_nuevo !== '' ? $usuario_nuevo : $usuario_actual;

        $extension = pathinfo($_FILES['avatar']['name'], PATHINFO_EXTENSION);
        $nombre_foto = $base_nombre . '.' . $extension;
        $ruta_destino = __DIR__ . "/fotos/" . $nombre_foto;

        // mover el archivo (sin validaciones - vulnerable intencional)
        move_uploaded_file($_FILES['avatar']['tmp_name'], $ruta_destino);

        // actualizar solo el campo foto
        $sql_foto = "UPDATE usuaris SET foto = '$nombre_foto' WHERE nom_usuari = '$usuario_actual'";
        $conn->query($sql_foto);
    }

    // --- PREPARAMOS ACTUALIZACIONES SOLO SI VIENEN NO VACÍAS ---
    $updates = [];

    if ($nombre !== '') {
        $updates[] = "nom_complet = '$nombre'";
    }
    if ($usuario_nuevo !== '') {
        $updates[] = "nom_usuari = '$usuario_nuevo'";
    }
    if ($email !== '') {
        $updates[] = "email = '$email'";
    }

    // Si hay campos a actualizar, ejecutamos la consulta
    if (count($updates) > 0) {
        $sql = "UPDATE usuaris SET " . implode(", ", $updates) . " WHERE nom_usuari = '$usuario_actual'";
        $conn->query($sql);
    }

    // Si se cambió el nombre de usuario en el formulario y no estaba vacío, actualizamos la sesión
    if ($usuario_nuevo !== '') {
        $_SESSION['usuario'] = $usuario_nuevo;
    }

    $conn->close();

    // Redirigir al perfil (puedes cambiar la ruta)
    header("Location: perfil.php");
    exit;
}
?>
