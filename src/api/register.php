<?php
// importaciones necesarios
require_once('../config/database.php');

// Obtener la URL solicitada
$request_uri = $_SERVER['REQUEST_URI'];

// Enrutamiento de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Manejar solicitudes POST
    if ($request_uri === '/api/register.php') {
        require_once('../controllers/UserController.php');
        $_POST = json_decode(file_get_contents('php://input'), true);
        $controller = new UserController($conexion);
        $controller->handleUserRegister($_POST);
    } else {
        http_response_code(404); // Endpoint no encontrado
    }
} else {
    http_response_code(405); // Método no permitido
}
?>

