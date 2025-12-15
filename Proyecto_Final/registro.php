<?php
session_start();
include 'conexion.php';
include 'idioma.php';

$mensaje = ''; // Variable para mensajes de error o éxito

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $usuario = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if($usuario && $password && $confirm_password){
        if($password === $confirm_password){
            // Verificar si el usuario ya existe
            $stmt = $conexion->prepare("SELECT id FROM usuarios WHERE username = ?");
            $stmt->bind_param("s", $usuario);
            $stmt->execute();
            $stmt->store_result();
            
            if($stmt->num_rows > 0){
                $mensaje = 'El usuario ya existe. Por favor elige otro.';
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $stmtInsert = $conexion->prepare("INSERT INTO usuarios (username, password) VALUES (?, ?)");
                $stmtInsert->bind_param("ss", $usuario, $hash);
                if($stmtInsert->execute()){
                    $mensaje = 'Usuario registrado correctamente. <a href="login.php">Iniciar sesión</a>';
                } else {
                    $mensaje = 'Error al registrar el usuario: ' . $conexion->error;
                }
                $stmtInsert->close();
            }
            $stmt->close();
        } else {
            $mensaje = 'Las contraseñas no coinciden.';
        }
    } else {
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
<div class="login-container">
    <div class="login-box">
        <h2><?= $lang_data['Registro'] ?></h2>

        <!-- Mostrar mensaje si existe -->
        <?php if($mensaje): ?>
            <p class="error"><?= $mensaje ?></p>
        <?php endif; ?>

        <!-- Formulario de registro -->
        <form method="POST">
            <input type="text" name="usuario" placeholder="<?= $lang_data['usuario'] ?>" required>
            <input type="password" name="password" placeholder="<?= $lang_data['contraseña'] ?>" required>
            <input type="password" name="confirm_password" placeholder="<?= $lang_data['Confirmar Contraseña'] ?>" required>
            <button type="submit"><?= $lang_data['Registrar'] ?></button>
            <button type="button" onclick="window.location.href='login.php'"><?= $lang_data['volver_login'] ?></button>

            <!-- Enlaces para cambiar idioma -->
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
