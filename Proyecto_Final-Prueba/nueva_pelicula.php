<?php
session_start();
include 'idioma.php';
include 'Peliculas.php';

// Protege la página: si no hay usuario logueado, redirige al login
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Inicializar lista películas en la sesión si no existe
if (!isset($_SESSION['peliculas'])) {
    $_SESSION['peliculas'] = [];
}

// Array para guardar valores ingresados en el formulario
$valores = [
    'titulo' => '',
    'año' => '',
    'director' => '',
    'actores' => '',
    'genero' => ''
];

// Array para almacenar errores de validación
$errores = [];

// Procesar el formulario cuando se envía mediante POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Tomar valores enviados y eliminar espacios extra
    $valores['titulo'] = trim($_POST['titulo'] ?? '');
    $valores['año'] = trim($_POST['año'] ?? '');
    $valores['director'] = trim($_POST['director'] ?? '');
    $valores['actores'] = trim($_POST['actores'] ?? '');
    $valores['genero'] = trim($_POST['genero'] ?? '');

    // Validar campos obligatorios
    if ($valores['titulo'] === '')  $errores[] = 'El título es obligatorio.';
    if ($valores['año'] === '' || !is_numeric($valores['año'])) $errores[] = 'El año es obligatorio y debe ser un número.';
    if ($valores['director'] === '') $errores[] = 'El director es obligatorio.';
    if ($valores['genero'] === '')  $errores[] = 'El género es obligatorio.';

    // Si no hay errores, crear nueva película como objeto
    if (empty($errores)) {
        $nueva_pelicula = new Pelicula(
            $valores['titulo'],
            (int)$valores['año'],
            $valores['director'],
            $valores['actores'],
            $valores['genero']
        );

        // Agregar el objeto al array de películas en la sesión
        $_SESSION['peliculas'][] = $nueva_pelicula; 

        // Mensaje flash de éxito
        $_SESSION['flash'][] = ['type' => 'success', 'text' => "✅ Película '{$valores['titulo']}' añadida correctamente."];
    
        // Redirigir a catálogo
        header('Location: catalogo.php');
        exit();
    } else {
        // Si hay errores, guardarlos en flash para mostrarlos en la página
        $_SESSION['flash'][] = ['type' => 'error', 'text' => implode('<br>', $errores)];
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $lang_data['nueva_pelicula'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?= $lang_data['añadir_pelicula'] ?></h1>

    <!-- Selector de idioma -->
    <div class="idiomas">
        🌐 
        <a href="idioma.php?lang=es">Español</a> | 
        <a href="idioma.php?lang=en">English</a>
    </div>

    <div class="container">
        <!-- Formulario para agregar nueva película -->
        <form method="POST" action="nueva_pelicula.php">
            <!-- Campos de formulario con valores guardados y traducciones -->
            <label><?= $lang_data['titulo'] ?></label>
            <input type="text" name="titulo" value="<?= htmlspecialchars($valores['titulo']) ?>">

            <label><?= $lang_data['año'] ?></label>
            <input type="text" name="año" value="<?= htmlspecialchars($valores['año']) ?>">

            <label><?= $lang_data['director'] ?></label>
            <input type="text" name="director" value="<?= htmlspecialchars($valores['director']) ?>">

            <label><?= $lang_data['actor'] ?></label>
            <input type="text" name="actores" value="<?= htmlspecialchars($valores['actores']) ?>">

            <label><?= $lang_data['generos'] ?></label>
            <input type="text" name="genero" value="<?= htmlspecialchars($valores['genero']) ?>">

            <!-- Botón de envío traducido -->
            <input type="submit" value="<?= $lang_data['añadir_pelicula'] ?>">
        </form>

        <br>
        <!-- Botón para regresar al catálogo -->
         <button class="boton-flecha" onclick="window.location.href='catalogo.php'"></button>
    </div>
</body>
</html>