<?php
// Incluir archivo de configuración de la base de datos
require_once('../config/database.php');

// Seleccionar la base de datos
$conexion->select_db("land_gorilla");

// Crear la tabla de roles si no existe
$query = "CREATE TABLE IF NOT EXISTS role (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL
)";

if ($conexion->query($query) === TRUE) {
    echo "Tabla de roles creada correctamente";
    
    // Insertar roles en la tabla
    $insert_query = "INSERT INTO role (name) VALUES 
                    ('común'),
                    ('comerciante')";

    if ($conexion->query($insert_query) === TRUE) {
        echo "Roles insertados correctamente";
    } else {
        echo "Error al insertar roles: " . $conexion->error;
    }
} else {
    echo "Error al crear la tabla de roles: " . $conexion->error;
}

// Cerrar la conexión
$conexion->close();
?>
