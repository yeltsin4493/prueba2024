<?php
class UserController
{
    private $conexion;

    // Constructor
    public function __construct($conexion)
    {
        $this->conexion = $conexion;
    }

    // Método para manejar la solicitud de registro de usuarios
    public function handleRegistrationRequest($request_data)
    {
        // Recopilar datos del formulario de registro
        $full_name = $request_data['full_name'];
        $document_id = $request_data['document_id'];
        $email = $request_data['email'];
        $password = $request_data['password'];
        $role_id = $request_data['role_id']; // Añadir el campo role_id al formulario de registro

        // Verificar si el documento de identidad ya está registrado
        $existing_user_document = $this->getUserByDocumentId($document_id);
        if ($existing_user_document) {
            echo json_encode(array('error' => 'El documento de identidad ya esta registrado'));
            return;
        }

        // Verificar si el correo electrónico ya está registrado
        $existing_user_email = $this->getUserByEmail($email);
        if ($existing_user_email) {
            echo json_encode(array('error' => 'El correo electronico ya esta registrado'));
            return;
        }

        // Insertar nuevo usuario en la base de datos
        $query = "INSERT INTO users (full_name, document_id, email, password, role_id) VALUES ('$full_name', '$document_id', '$email', '$password', '$role_id')";

        if ($this->conexion->query($query) === TRUE) {
            echo json_encode(array('message' => 'Registro exitoso'));
        } else {
            echo json_encode(array('error' => 'Error al registrar usuario'));
        }
    }

     // Método para manejar la solicitud de inicio de sesión
     public function handleLoginRequest($request_data) {
        // Recopilar datos del formulario de inicio de sesión
        $email = $request_data['email'];
        $password = $request_data['password'];

        // Verificar las credenciales del usuario
        $user = $this->getUserByEmailAndPassword($email, $password);

        if ($user) {
            // Usuario autenticado
            echo json_encode(array('message' => 'Inicio de sesión exitoso', 'user' => $user));
        } else {
            // Credenciales incorrectas
            echo json_encode(array('error' => 'Credenciales incorrectas'));
        }
    }

    // Método para obtener un usuario por su correo electrónico y contraseña
    private function getUserByEmailAndPassword($email, $password) {
        // Realizar consulta a la base de datos para verificar las credenciales
        $query = "SELECT * FROM users WHERE email = '$email' AND password = '$password'";
        $result = $this->conexion->query($query);
        return $result->fetch_assoc();
    }

    // Método para obtener un usuario por su documento de identidad
    private function getUserByDocumentId($document_id)
    {
        $query = "SELECT * FROM users WHERE document_id = '$document_id'";
        $result = $this->conexion->query($query);
        return $result->fetch_assoc();
    }

    // Método para obtener un usuario por su correo electrónico
    private function getUserByEmail($email)
    {
        $query = "SELECT * FROM users WHERE email = '$email'";
        $result = $this->conexion->query($query);
        return $result->fetch_assoc();
    }
}
