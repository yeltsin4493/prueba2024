<?php
require_once('../services/UserService.php');

class UserController
{
    private $userService;

    public function __construct($conexion)
    {
        $this->userService = new UserService($conexion);
    }

    public function handleUserLogin($request_data)
    {
        $this->userService->handleLoginRequest($request_data);

    }

    public function handleUserRegister($request_data)
    {
        $this->userService->handleRegistrationRequest($request_data);

    }
}
