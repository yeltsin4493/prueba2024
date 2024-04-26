<?php
require_once('../services/UserService.php');
require_once('../utils/JWTGenerator.php');

class UserController
{
    private $userService;

    public function __construct($conexion)
    {
        $this->userService = new UserService($conexion);
    }

    public function handleUserLogin($request_data)
    {
        $user = $this->userService->handleLoginRequest($request_data);

        if ($user) {
            // Usuario autenticado, genera el token JWT
            $jwt_token = generateJWT($user);
            echo json_encode(array('message' => 'Inicio de sesión exitoso', 'token' => $jwt_token));
        } else {
            // Credenciales incorrectas
            echo json_encode(array('error' => 'Credenciales incorrectas'));
        }
    }

    public function handleUserRegister($request_data)
    {
        $this->userService->handleRegistrationRequest($request_data);
    }
}
