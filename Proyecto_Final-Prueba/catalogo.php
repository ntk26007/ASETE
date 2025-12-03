<?php 
session_start();
require_once 'Peliculas.php';
require_once 'DB.php';
include 'idioma.php';
include 'Utils.php';
include 'conexion.php';

// Proteger página
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// --- Contador de visitas ---
if (!isset($_SESSION['visitas'])) $_SESSION['visitas'] = 0;
$visitas = &$_SESSION['visitas'];
Utils::incrementarVisitas($visitas);

// --- Inicializar DB ---
$db = new DB($conexion);

// ===============================
//  RESERVAR o DEVOLVER
// ===============================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tabla = $_POST['tabla'];
    $id = (int)$_POST['id'];

    if (isset($_POST['reservar'])) {
        $db->cambiarEstado($tabla, $id, 'Reservado');

        $mensaje = ($tabla === 'Peliculas') ? 
            ($lang_data['mensaje_reservar_pelicula'] ?? "📕 Película reservada correctamente.") : 
            ($lang_data['mensaje_reservar_libro'] ?? "📕 Libro reservado correctamente.");

        $_SESSION['flash'][] = [
            'type' => 'success',
            'text' => $mensaje
        ];
    }

    if (isset($_POST['devolver'])) {
        $db->cambiarEstado($tabla, $id, 'Disponible');

        $mensaje = ($tabla === 'Peliculas') ? 
            ($lang_data['mensaje_devolver_pelicula'] ?? "🔄 Película devuelta correctamente.") : 
            ($lang_data['mensaje_devolver_libro'] ?? "🔄 Libro devuelto correctamente.");

        $_SESSION['flash'][] = [
            'type' => 'info',
            'text' => $mensaje
        ];
    }

    header("Location: catalogo.php");
    exit();
}


// --- Filtros desde index.php ---
$filtros = [
    'titulo' => $_GET['titulo'] ?? '',
    'genero' => $_GET['genero'] ?? '',
    'año' => $_GET['año'] ?? '',
    'director' => $_GET['director'] ?? '',
    'actor' => $_GET['actor'] ?? '',
    'editorial' => $_GET['editorial'] ?? ''
];

// --- Datos ---
$peliculas = $db->getPeliculas($filtros);
$libros = $db->getLibros($filtros);

// --- Mensajes flash ---
$mensaje = '';
if (isset($_SESSION['flash'])) {
    foreach ($_SESSION['flash'] as $flash) {
        $mensaje .= "<p class='{$flash['type']}'>{$flash['text']}</p>";
    }
    unset($_SESSION['flash']);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Catálogo</title>
<link rel="stylesheet" href="style.css">

<style>
.catalogo-container {
    display: flex;
    gap: 20px;
    align-items: flex-start;
}
.catalogo-col {
    flex: 1;
}
</style>

</head>
<body>

<h1><?= $lang_data['titulo_catalogo'] ?></h1>

<div class="idiomas">
    🌐 
    <a href="idioma.php?lang=es">Español</a> | 
    <a href="idioma.php?lang=en">English</a>
</div>

<div class="container">
    <button class="boton-flecha" onclick="window.location.href='index.php'"></button>
    <button class="nueva-box" onclick="window.location.href='nueva_pelicula.php'"><?= $lang_data["nueva_pelicula"] ?></button>
    <button class="nueva-box" onclick="window.location.href='logout.php'"><?= $lang_data["cerrar_sesion"] ?></button>
</div>

<div class="resultados">
    <?= $mensaje ?>

    <div class="catalogo-container">

    <!-- ===================== -->
    <!--       PELÍCULAS       -->
    <!-- ===================== -->
    <div class="catalogo-col">
        <h2>🎬 <?= $lang_data['peliculas'] ?></h2>

        <?php if (count($peliculas) > 0): ?>
            <?php foreach ($peliculas as $p): ?>
                <?= $p->aHTML($lang_data) ?>

                <!-- BOTONES DE RESERVAR / DEVOLVER -->
                <?php if ($p->estado === 'Disponible'): ?>
                    <form method="POST">
                        <input type="hidden" name="tabla" value="Peliculas">
                        <input type="hidden" name="id" value="<?= $p->id ?>">
                        <input type="submit" name="reservar" class="boton boton-reservar" value="<?= $lang_data['reservar'] ?>">
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="tabla" value="Peliculas">
                        <input type="hidden" name="id" value="<?= $p->id ?>">
                        <input type="submit" name="devolver" class="boton boton-devolver" value="<?= $lang_data['devolver'] ?>">
                    </form>
                <?php endif; ?>

            <?php endforeach; ?>
        <?php else: ?>
            <p>❌ <?= $lang_data['no_hay_peliculas'] ?? 'No hay películas.' ?></p>
        <?php endif; ?>
    </div>

    <!-- ===================== -->
    <!--         LIBROS        -->
    <!-- ===================== -->
    <div class="catalogo-col">
        <h2>📚 <?= $lang_data['libros'] ?></h2>

        <?php if (count($libros) > 0): ?>
            <?php foreach ($libros as $l): ?>
                <?= $l->aHTML($lang_data) ?>

                <!-- BOTONES DE RESERVAR / DEVOLVER -->
                <?php if ($l->estado === 'Disponible'): ?>
                    <form method="POST">
                        <input type="hidden" name="tabla" value="Libros">
                        <input type="hidden" name="id" value="<?= $l->id ?>">
                        <input type="submit" name="reservar" class="boton boton-reservar" value="<?= $lang_data['reservar'] ?>">
                    </form>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="tabla" value="Libros">
                        <input type="hidden" name="id" value="<?= $l->id ?>">
                        <input type="submit" name="devolver" class="boton boton-devolver" value="<?= $lang_data['devolver'] ?>">
                    </form>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>❌ <?= $lang_data['no_hay_libros'] ?? 'No hay libros.' ?></p>
        <?php endif; ?>
    </div>
</div>
    <p>📊 Has visitado el catálogo <strong><?= $_SESSION['visitas'] ?></strong> veces.</p>
</div>
</body>
</html>