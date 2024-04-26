# Proyecto Land Gorilla

Este proyecto utiliza Docker y Docker Compose para facilitar el despliegue y la gestión del entorno de desarrollo.

## Configuración inicial

1. Clonar el repositorio:

    ```bash
    git clone <url_del_repositorio>
    ```

2. En una terminal dentro del directorio del proyecto, ejecutar el siguiente comando para construir y levantar los contenedores Docker:

    ```bash
    docker-compose up --build
    ```

## Gestión de la base de datos

1. Abrir otra terminal y ejecutar el siguiente comando para acceder a la base de datos MySQL:

    ```bash
    docker-compose exec mysql_db mysql -uroot -proot
    ```

2. Crear la base de datos `land_gorilla` si no existe:

    ```sql
    CREATE DATABASE IF NOT EXISTS land_gorilla;
    ```

3. Migrar las tablas ejecutando los scripts correspondientes. Desde otra terminal:

    ```bash
    docker-compose exec php-env bash
    ```

    Luego, dentro del contenedor, navegar hasta la carpeta de migraciones:

    ```bash
    cd migrations
    ```

    Y ejecutar los siguientes comandos para crear las tablas necesarias:

    ```bash
    php create_role_table.php
    php create_users_table.php
    php create_wallets_table.php
    php create_transactions_table.php
    ```

## Uso de endpoints

A continuación, se detallan los endpoints disponibles para interactuar con la API:

### Registro de usuarios

- Método: `POST`
- URL: `http://localhost:9000/api/register.php`
- Cuerpo de la solicitud:

    ```json
    {
        "full_name": "Yeltsin",
        "document_id": "12344",
        "email": "124@example.com",
        "password": "contraseña123",
        "role_id": 2
    }
    ```

    **Nota:** El `role_id` debe ser `1` para una persona común y `2` para un comerciante.

### Inicio de sesión

- Método: `POST`
- URL: `http://localhost:9000/api/login.php`
- Cuerpo de la solicitud:

    ```json
    {
        "email": "correo@example.com",
        "password": "contraseña123"
    }
    ```

    **Nota:** El `role_id` debe ser `1` para una persona común y `2` para un comerciante.

### Realizar transacción

- Método: `POST`
- URL: `http://localhost:9000/api/transactions.php`
- Headers:
    - Authorization: Bearer [token]
- Cuerpo de la solicitud:

    ```json
    {
        "sender_id": 4,
        "receiver_id": 2,
        "amount": 1.00
    }
    ```

### Actualizar saldo de billetera

- Método: `POST`
- URL: `http://localhost:9000/api/wallet.php`
- Headers:
    - Authorization: Bearer [token]
- Cuerpo de la solicitud:

    ```json
    {
        "user_id": 4,
        "amount": 50.00
    }
    ```

## Notas adicionales

- Antes de consumir cualquier endpoint que requiera autorización, asegúrate de incluir el token correspondiente en el encabezado de autorización.
