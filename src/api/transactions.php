<?php
// Incluir archivos necesarios y configuraciones
require_once('../config/database.php');
// require_once('./controllers/UserController.php');

// Obtener la URL solicitada
$request_uri = $_SERVER['REQUEST_URI'];

// Enrutamiento de solicitudes
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Manejar solicitudes POST
    if ($request_uri === '/api/transactions.php') {
        require_once('../controllers/TransactionController.php');
        $_POST = json_decode(file_get_contents('php://input'), true);
        $controller = new TransactionController($conexion);
        $controller->handleTransaction($_POST);
    } else {
        http_response_code(404); // Endpoint no encontrado
    }
} else {
    http_response_code(405); // Método no permitido
}
