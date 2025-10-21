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
    $nombre = $_POST['nombre'] ? $_POST['nombre'] : '';
    $usuario_nuevo = $_POST['usuario'] ? $_POST['usuario'] : '';
    $email = $_POST['email'] ? $_POST['email'] : '';
    $password = $_POST['password'] ? $_POST['password'] : '';

    // --- FOTO ---
if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $nombre_foto = $_FILES['avatar']['name'];       // nombre original (vulnerable)
        $dest = './fotos/' . $nombre_foto;              // ruta relativa sencilla
        move_uploaded_file($_FILES['avatar']['tmp_name'], $dest);

        // Guardamos solo el nombre de archivo en la BD (campo foto)
        
        $sql = "UPDATE usuaris SET foto_perfil = '$dest' WHERE nom_usuari = '$usuario_actual'";
        $conn->query($sql);
    }

    // --- PREPARAMOS ACTUALIZACIONES SOLO SI VIENEN NO VACÍAS ---
if ($nombre !== '') {
        $sql = "UPDATE usuaris SET nom_complet = '$nombre' WHERE nom_usuari = '$usuario_actual'";
        $conn->query($sql);
    }

    if ($email !== '') {
        $sql = "UPDATE usuaris SET email = '$email' WHERE nom_usuari = '$usuario_actual'";
        $conn->query($sql);
    }

    if ($password !== '') {
        $sql = "UPDATE usuaris SET password_hash = '$password' WHERE nom_usuari = '$usuario_actual'";
        $conn->query($sql);
    }

    // IMPORTANTE: actualizamos el nom_usuari AL FINAL para que las anteriores queries
    // sigan encontrando la fila por el nombre antiguo.
    if ($usuario_nuevo !== '') {
        $sql = "UPDATE usuaris SET nom_usuari = '$usuario_nuevo' WHERE nom_usuari = '$usuario_actual'";
        $conn->query($sql);

        // actualizar la sesión con el nuevo usuario
        $_SESSION['usuario'] = $usuario_nuevo;
    }

    $conn->close();

    header('Location: perfil.php');
    exit;
}
?>