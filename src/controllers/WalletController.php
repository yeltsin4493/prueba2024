<?php
require_once('../services/WalletService.php');
class WalletController
{
    private $walletService;

    public function __construct($conexion)
    {
        $this->walletService = new WalletService($conexion);
    }

    public function handleWallet($request_data)
    {
        $this->walletService->addBalance($request_data);

    }
}
