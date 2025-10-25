<?php
// db_pdo.php
// Dades de connexió
$servidor = 'localhost';
$bd = 'plataforma_videojocs';
$usuari = 'plataforma_user';
$contrasenya = '123456789a';

try {
    $pdo = new PDO("mysql:host=$servidor;dbname=$bd", $usuari, $contrasenya);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Error de connexió: " . $e->getMessage();
    exit();
}