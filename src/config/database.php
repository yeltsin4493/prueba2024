<?php
$host = 'mysql_db';
$username = 'root';
$password = 'root';
$database = 'land_gorilla';

$conexion = new mysqli($host, $username, $password, $database);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}
?>
