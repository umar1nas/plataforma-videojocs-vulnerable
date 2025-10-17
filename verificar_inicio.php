    <?php
    require "./backend/include/db_mysqli.php";
session_start();

    if (isset($_POST['usuario'], $_POST['password'])) {
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        // Consulta directa (vulnerable a SQL injection)
        $sql = "SELECT * FROM usuaris WHERE nom_usuari = '$usuario' and password_hash = '$password'";
        $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
                $_SESSION['usuario'] = $usuario;

        header('Location:./backend/plataforma.php');
    }
    else{
        header('Location:./error_inicio.php');
    }
    $conn->close();
    }
    ?>
