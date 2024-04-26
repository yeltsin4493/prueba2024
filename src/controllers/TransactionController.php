<?php
require_once('../services/TransactionService.php');
class TransactionController
{
    private $transactionService;

    public function __construct($conexion)
    {
        $this->transactionService = new TransactionService($conexion);
    }

    public function handleTransaction($request_data)
    {
        $this->transactionService->handleTransactionRequest($request_data);

    }
}
