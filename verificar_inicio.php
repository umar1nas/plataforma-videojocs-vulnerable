<?php
require "./backend/include/db_mysqli.php";
session_start();

if (isset($_POST['usuario'], $_POST['password'])) {
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    // Consulta directa vulnerable
    $sql = "SELECT * FROM usuaris WHERE nom_usuari = '$usuario' and password_hash = '$password'";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['usuario'] = $usuario;               // Guarda el nombre de usuario como antes (opcional)
        $_SESSION['usuari_id'] = $row['id'];           // Aquí se guarda el id real del usuario (AUTOINCREMENT de BBDD)
        header('Location:./backend/plataforma.php');
        exit();
    } else {
        header('Location:./error_inicio.php');
        exit();
    }
    $conn->close();
}
?>