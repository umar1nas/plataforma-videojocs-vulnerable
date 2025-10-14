<?php
require "./db_mysqli.php";

if (isset($_POST['nombre'],$_POST['usuario'], $_POST['email'], $_POST['password'])) {
  $nombre = $_POST['nombre'];
  $nom_usuari = $_POST['usuario'];
  $email = $_POST['email'];
  $password = $_POST['password'];
  $sql = "INSERT INTO usuaris (nom_usuari, email, password_hash, nom_complet) VALUES ('$nom_usuari', '$email', '$password', '$nombre')";
  $conn->query($sql);
  echo "<p>Usuari afegit a la base de dades!</p>";
  $conn->close();
}
?>