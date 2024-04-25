<?php
class TransactionController
{
    private $conexion;

    // Constructor
    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Método para manejar la solicitud de transferencia de dinero
    public function handleTransactionRequest($request_data)
    {
        // Verificar si se proporcionaron los campos necesarios en la solicitud
        if (!isset($request_data['sender_id']) || !isset($request_data['receiver_id']) || !isset($request_data['amount'])) {
            echo json_encode(array('error' => 'Faltan campos obligatorios en la solicitud'));
            return;
        }

        // Recopilar datos de la solicitud de transferencia
        $sender_id = $request_data['sender_id'];
        $receiver_id = $request_data['receiver_id'];
        $amount = $request_data['amount'];

        // Verificar si el usuario emisor tiene saldo suficiente
        $payer_balance = $this->getUserBalance($sender_id);
        if ($payer_balance < $amount) {
            echo json_encode(array('error' => 'Saldo insuficiente'));
            return;
        }

        // Realizar la transferencia
        $success = $this->performTransaction($sender_id, $receiver_id, $amount);

        if ($success) {
            echo json_encode(array('message' => 'Transferencia exitosa'));
        } else {
            echo json_encode(array('error' => 'Error al realizar la transferencia'));
        }
    }

    // Método para obtener el saldo de un usuario
    private function getUserBalance($user_id)
    {
        $query = "SELECT balance FROM wallets WHERE user_id = $user_id";
        $result = $this->conexion->query($query);

        // Verificar si se encontró un resultado
        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['balance'];
        } else {
            // Manejar el caso donde no se encontró ningún resultado
            // Puedes devolver un valor predeterminado, como 0
            return 0;
        }
    }


    // Método para realizar la transferencia de dinero
    private function performTransaction($sender_id, $receiver_id, $amount)
    {
        // Iniciar una transacción en la base de datos
        $this->conexion->begin_transaction();

        // Actualizar el saldo del usuario emisor (restar el valor)
        $update_sender_query = "UPDATE wallets SET balance = balance - $amount WHERE user_id = $sender_id";
        $this->conexion->query($update_sender_query);

        // Verificar si el usuario receptor tiene una entrada en la tabla wallets
        $check_receiver_query = "SELECT * FROM wallets WHERE user_id = $receiver_id";
        $result = $this->conexion->query($check_receiver_query);

        if ($result && $result->num_rows > 0) {
            // El usuario receptor ya tiene una entrada en la tabla wallets, actualizar el saldo
            $update_receiver_query = "UPDATE wallets SET balance = balance + $amount WHERE user_id = $receiver_id";
            $this->conexion->query($update_receiver_query);
        } else {
            // El usuario receptor no tiene una entrada en la tabla wallets, crear una nueva
            $insert_receiver_query = "INSERT INTO wallets (user_id, balance) VALUES ($receiver_id, $amount)";
            $this->conexion->query($insert_receiver_query);
        }

        // Registrar la transferencia en la tabla de transacciones
        $insert_transaction_query = "INSERT INTO transactions (sender_id, receiver_id, amount) VALUES ($sender_id, $receiver_id, $amount)";
        $this->conexion->query($insert_transaction_query);

        // Verificar si ocurrieron errores durante la transacción
        if ($this->conexion->errno) {
            // Revertir la transacción
            $this->conexion->rollback();
            return false;
        }

        // Confirmar la transacción
        $this->conexion->commit();
        return true;
    }
}
