<?php
// Inicia la sesión para poder usar variables de sesión si fuera necesario
session_start();

// Incluye el archivo de conexión a la base de datos
include 'conexion.php';

// Incluye el archivo de idioma para mostrar textos traducidos
include 'idioma.php';

// Variable que almacenará mensajes de error o éxito para mostrar al usuario
$mensaje = '';

// Se comprueba si el formulario ha sido enviado mediante el método POST
if($_SERVER['REQUEST_METHOD'] === 'POST'){

    // Se obtiene el nombre de usuario del formulario y se eliminan espacios en blanco
    $usuario = trim($_POST['usuario'] ?? '');

    // Se obtiene la contraseña introducida por el usuario
    $password = $_POST['password'] ?? '';

    // Se obtiene la confirmación de la contraseña
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Se comprueba que todos los campos tengan contenido
    if($usuario && $password && $confirm_password){

        // Se comprueba que ambas contraseñas coincidan
        if($password === $confirm_password){

            /* ==================================================
               COMPROBAR SI EL USUARIO YA EXISTE
               ================================================== */

            // Se prepara una consulta para buscar si el nombre de usuario ya está registrado
            $stmt = $conexion->prepare(
                "SELECT id FROM usuarios WHERE username = ?"
            );

            // Se asocia el nombre de usuario al parámetro de la consulta
            $stmt->bind_param("s", $usuario);

            // Se ejecuta la consulta
            $stmt->execute();

            // Se almacenan los resultados para poder comprobar el número de filas
            $stmt->store_result();

            // Si existe al menos una fila, el usuario ya existe
            if($stmt->num_rows > 0){
                $mensaje = 'El usuario ya existe. Por favor elige otro.';
            } else {

                /* ==================================================
                   REGISTRO DEL NUEVO USUARIO
                   ================================================== */

                // Se genera un hash seguro de la contraseña usando password_hash
                // Esto evita almacenar contraseñas en texto plano
                $hash = password_hash($password, PASSWORD_DEFAULT);

                // Se prepara la consulta para insertar el nuevo usuario en la base de datos
                $stmtInsert = $conexion->prepare(
                    "INSERT INTO usuarios (username, password) VALUES (?, ?)"
                );

                // Se asocian el nombre de usuario y la contraseña cifrada
                $stmtInsert->bind_param("ss", $usuario, $hash);

                // Se ejecuta la inserción
                if($stmtInsert->execute()){
                    // Mensaje de éxito con enlace al login
                    $mensaje = 'Usuario registrado correctamente. <a href="login.php">Iniciar sesión</a>';
                } else {
                    // Mensaje de error si falla la inserción
                    $mensaje = 'Error al registrar el usuario: ' . $conexion->error;
                }

                // Se cierra la sentencia de inserción
                $stmtInsert->close();
            }

            // Se cierra la sentencia de comprobación
            $stmt->close();

        } else {
            // Mensaje si las contraseñas no coinciden
            $mensaje = 'Las contraseñas no coinciden.';
        }

    } else {
        // Mensaje si algún campo está vacío
        $mensaje = 'Por favor, complete todos los campos.';
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Registro</title>
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Contenedor principal del formulario de registro -->
<div class="login-container">
    <div class="login-box">

        <!-- Título de la página de registro -->
        <h2><?= $lang_data['Registro'] ?></h2>

        <!-- Mostrar mensaje de error o éxito si existe -->
        <?php if($mensaje): ?>
            <p class="error"><?= $mensaje ?></p>
        <?php endif; ?>

        <!-- Formulario de registro de usuario -->
        <form method="POST">

            <!-- Campo para introducir el nombre de usuario -->
            <input type="text" name="usuario" 
                   placeholder="<?= $lang_data['usuario'] ?>" required>

            <!-- Campo para introducir la contraseña -->
            <input type="password" name="password" 
                   placeholder="<?= $lang_data['contraseña'] ?>" required>

            <!-- Campo para confirmar la contraseña -->
            <input type="password" name="confirm_password" 
                   placeholder="<?= $lang_data['Confirmar Contraseña'] ?>" required>

            <!-- Botón para enviar el formulario -->
            <button type="submit"><?= $lang_data['Registrar'] ?></button>

            <!-- Botón para volver a la página de login -->
            <button type="button" 
                    onclick="window.location.href='login.php'">
                <?= $lang_data['volver_login'] ?>
            </button>

            <!-- Enlaces para cambiar el idioma de la interfaz -->
            <div class="idiomas">
                🌐 
                <a href="idioma.php?lang=es">Español</a> | 
                <a href="idioma.php?lang=en">English</a>
            </div>

        </form>
    </div>
</div>

</body>
</html>
