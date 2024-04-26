<?php
// importaciones necesarios
require_once('../config/database.php');

// Seleccionar la base de datos
$conexion->select_db("land_gorilla");

// Crear la tabla de usuarios si no existe
$query = "CREATE TABLE IF NOT EXISTS transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (receiver_id) REFERENCES users(id) ON DELETE CASCADE
    )";

if ($conexion->query($query) === TRUE) {
    echo "Tabla creada correctamente";
} else {
    echo "Error al crear la tabla: " . $conexion->error;
}

// Cerrar la conexión
$conexion->close();
?>
