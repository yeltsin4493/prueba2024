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

        // Verificar si el receptor es un comerciante
        if ($this->isMerchant($sender_id)) {
            echo json_encode(array('error' => 'Los comerciantes solo pueden recibir transferencias'));
            return;
        }

        // Realizar una solicitud al servicio de autorización externo
        $authorization_result = $this->authorizeTransaction();

        // Verificar la respuesta del servicio de autorización
        if ($authorization_result === 'Autorizado') {
            // Realizar la transferencia
            $success = $this->performTransaction($sender_id, $receiver_id, $amount);

            if ($success) {
                echo json_encode(array('message' => 'Transferencia exitosa'));
            } else {
                echo json_encode(array('error' => 'Error al realizar la transferencia'));
            }
        } else {
            echo json_encode(array('error' => 'La transferencia no está autorizada'));
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

        try {
            // Actualizar el saldo del usuario emisor (restar el valor)
            $update_sender_query = "UPDATE wallets SET balance = balance - $amount WHERE user_id = $sender_id";
            $this->conexion->query($update_sender_query);

            // Verificar si ocurrió algún error al actualizar el saldo del usuario emisor
            if ($this->conexion->errno) {
                throw new Exception("Error al actualizar el saldo del usuario emisor");
            }

            // Verificar si el usuario receptor ya tiene una entrada en la tabla wallets
            $check_receiver_query = "SELECT * FROM wallets WHERE user_id = $receiver_id FOR UPDATE";
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
            
            // Enviar notificación
            $notification_sent = $this->sendNotification();

            if ($notification_sent) {
                return true;
            } else {
                // Si falla el envío de notificación, podría considerarse una transacción fallida
                // Revertir la transacción
                $this->conexion->rollback();
                return false;
            }
        } catch (Exception $e) {
            // Revertir la transacción en caso de error
            $this->conexion->rollback();
            return false;
        }
    }


    // Método para verificar si un usuario es un comerciante
    private function isMerchant($user_id)
    {
        $query = "SELECT role_id FROM users WHERE id = $user_id";
        $result = $this->conexion->query($query);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['role_id'] == 2; // Suponiendo que el ID del rol de comerciante es 2
        }

        return false;
    }

    // Método para realizar una solicitud al servicio de autorización externo
    private function authorizeTransaction()
    {
        // URL del servicio de autorización externo
        $authorization_url = 'https://run.mocky.io/v3/1f94933c-353c-4ad1-a6a5-a1a5ce2a7abe';

        // Realizar la solicitud HTTP
        $authorization_response = file_get_contents($authorization_url);

        // Decodificar la respuesta JSON
        $authorization_result = json_decode($authorization_response, true);

        // Devolver el resultado de la autorización
        return isset($authorization_result['message']) ? $authorization_result['message'] : 'Error en la autorización';
    }

    // Método para enviar la notificación utilizando el servicio de terceros
    private function sendNotification()
    {
        // Simular el envío de notificación utilizando el enlace proporcionado
        $url = 'https://run.mocky.io/v3/6839223e-cd6c-4615-817a-60e06d2b9c82';
        $response = file_get_contents($url);

        // Verificar la respuesta del servicio de terceros
        if ($response && json_decode($response)->message) {
            return true; // Notificación enviada con éxito
        } else {
            return false; // Error al enviar la notificación
        }
    }
}
