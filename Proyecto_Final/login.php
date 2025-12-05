<?php
session_start();
include 'conexion.php';
include 'idioma.php';

$mensaje = ''; // Variable para almacenar mensajes de error

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    //login con base de datos
    if(isset($_POST['usuario']) && isset($_POST['password'])){
        $usuario = $_POST['usuario'];
        $password = $_POST['password'];

        // Preparar la consulta SQL para evitar inyecciones SQL
        $stmt = $conexion->prepare("SELECT id, username, password FROM usuarios WHERE username = ?");
        $stmt->bind_param("s", $usuario);
        $stmt->execute();
        $resultado = $stmt->get_result();

        // Verificar si se encontró un usuario con las credenciales proporcionadas
        if($resultado->num_rows === 1){
             $usuarioDB = $resultado->fetch_assoc();

            //Verificar contraseña correctamente
            if (password_verify($password, $usuarioDB['password'])) {

                $_SESSION['idCliente'] = $usuarioDB['id'];
                $_SESSION['usuario'] = $usuarioDB['username'];
                header("Location: index.php");
                exit();

            } else {
                $mensaje = '❌ Contraseña incorrecta.';
            }

        } else {
            $mensaje = '❌ El usuario no existe.';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body>
    <!-- Fondo animado con burbujas-->
    <div class="bg-bubble bg1"></div>
    <div class="bg-bubble bg2"></div>
    <div class="bg-bubble bg3"></div>

    <!-- Canvas de partículas animadas-->
    <canvas id="particles"></canvas>

    <!-- Caja principal del login -->
    <div class="login-box">
        <img src="https://cdn-icons-png.flaticon.com/512/847/847969.png" alt="Avatar">
        <h2><?= $lang_data['iniciar_sesion'] ?></h2>

        <!-- Mostrar mensaje de error si existe -->
        <?php if ($mensaje): ?>
            <p class="error"><?= htmlspecialchars($mensaje) ?></p>
        <?php endif; ?>

        <!-- Formulario de login -->
        <form method="POST" action="login.php">
            <!-- Input usuario -->
            <div class="input-group">
                <input type="text" id="usuario" name="usuario" placeholder=<?= $lang_data['usuario'] ?> required>
                <i class="fas fa-user"></i><!-- Icono de usuario -->
            </div>
            <!-- Input contraseña -->
            <div class="input-group">
                <input type="password" id="password" name="password" placeholder=<?= $lang_data['contraseña'] ?> required>
                <i class="fas fa-lock"></i> <!-- Icono de candado -->
            </div>

            <!-- Botón de login -->
            <button class="boton" type="submit"><?= $lang_data['login_boton'] ?></button>
             <!-- Botón para ir a registro -->
            <button class="boton" type="button" onclick="window.location.href='registro.php'"><?= $lang_data['registrarse'] ?></button>

            <!-- Extras: recordarme y enlace "olvidaste contraseña" -->
            <div class="extras">
                <label><input type="checkbox"><?= $lang_data['recordarme'] ?></label><br>
                <a href="#"><?= $lang_data['olvidaste'] ?></a>
            </div>

            <!-- Enlaces para cambiar idioma -->
            <div class="idiomas">
                🌐 
                <a href="idioma.php?lang=es">Español</a> | 
                <a href="idioma.php?lang=en">English</a>
            </div>
        </form>
    </div>

    <!-- Script de partículas -->
    <script>
        const canvas = document.getElementById('particles');
        const ctx = canvas.getContext('2d');

        let particles = []; // Array de partículas
        const numParticles = 100; // Número de partículas

        // Ajustar canvas al tamaño de la ventana
        function resizeCanvas() {
            canvas.width = window.innerWidth;
            canvas.height = window.innerHeight;
        }
        window.addEventListener('resize', resizeCanvas);
        resizeCanvas();

        // Inicializar partículas con posición, tamaño, velocidad y color aleatorios
        for (let i = 0; i < numParticles; i++) {
            particles.push({
                x: Math.random() * canvas.width,
                y: Math.random() * canvas.height,
                size: Math.random() * 2 + 1,
                speedX: (Math.random() - 0.5) * 0.8,
                speedY: (Math.random() - 0.5) * 0.8,
                color: `rgba(255, ${100 + Math.random()*155}, 255, 0.6)`
            });
        }

        // Función de animación
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height); // Limpiar canvas
            particles.forEach(p => {
                ctx.beginPath();
                ctx.arc(p.x, p.y, p.size, 0, Math.PI * 2); // Dibujar círculo
                ctx.fillStyle = p.color;
                ctx.fill();

                // Actualizar posición según velocidad
                p.x += p.speedX;
                p.y += p.speedY;

                // Rebotar en los bordes del canvas
                if (p.x < 0 || p.x > canvas.width) p.speedX *= -1;
                if (p.y < 0 || p.y > canvas.height) p.speedY *= -1;
            });
            
            requestAnimationFrame(animate); // Llamar de nuevo para animación continua
        }

        animate();// Iniciar animación
    </script>
</body>
</html>
