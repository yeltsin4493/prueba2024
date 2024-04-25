<?php
class WalletService
{
    private $conexion;

    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    public function addBalance($request_data)
    {
        // Verificar si se proporcionaron los campos necesarios en la solicitud
        if (!isset($request_data['user_id']) || !isset($request_data['amount'])) {
            echo json_encode(array('error' => 'Faltan campos obligatorios en la solicitud'));
            return;
        }

        $user_id = $request_data['user_id'];
        $amount = $request_data['amount'];

        // Verificar si el usuario existe
        if (!$this->userExists($user_id)) {
            echo json_encode(array('error' => 'El usuario no existe'));
            return;
        }

        // Verificar si el usuario tiene una entrada en la tabla wallets
        $wallet_query = "SELECT * FROM wallets WHERE user_id = $user_id";
        $wallet_result = $this->conexion->query($wallet_query);

        if ($wallet_result && $wallet_result->num_rows > 0) {
            // El usuario ya tiene una entrada en la tabla wallets, actualizar el saldo
            $update_query = "UPDATE wallets SET balance = balance + $amount WHERE user_id = $user_id";

            if ($this->conexion->query($update_query) === TRUE) {
                echo json_encode(array('message' => 'Saldo agregado correctamente'));
            } else {
                echo json_encode(array('error' => 'Error al agregar saldo'));
            }
        } else {
            // El usuario no tiene una entrada en la tabla wallets, crear una nueva
            $insert_query = "INSERT INTO wallets (user_id, balance) VALUES ($user_id, $amount)";

            if ($this->conexion->query($insert_query) === TRUE) {
                echo json_encode(array('message' => 'Saldo agregado correctamente'));
            } else {
                echo json_encode(array('error' => 'Error al agregar saldo'));
            }
        }
    }

    private function userExists($user_id)
    {
        $query = "SELECT * FROM users WHERE id = $user_id";
        $result = $this->conexion->query($query);
        return $result && $result->num_rows > 0;
    }
}
