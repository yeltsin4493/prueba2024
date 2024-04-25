<?php
// Incluir archivo de configuración de la base de datos
require_once('../config/database.php');

// Seleccionar la base de datos
$conexion->select_db("land_gorilla");

// Crear la tabla de usuarios si no existe
$query = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    document_id VARCHAR(20) UNIQUE NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conexion->query($query) === TRUE) {
    echo "Tabla creada correctamente";
} else {
    echo "Error al crear la tabla: " . $conexion->error;
}

// Cerrar la conexión
$conexion->close();
?>
