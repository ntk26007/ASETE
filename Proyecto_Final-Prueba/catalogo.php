<?php 
session_start();
require_once 'Peliculas.php';
require_once 'DB.php';
include 'idioma.php';
include 'Utils.php';
include 'conexion.php';

// Protege la página
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// --- Contador de visitas ---
if (!isset($_SESSION['visitas'])) $_SESSION['visitas'] = 0;
$visitas = &$_SESSION['visitas'];
Utils::incrementarVisitas($visitas);

// --- Cambiar estado si se recibe POST ---
$db = new DB($conexion);
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reservar'])) {
    $tabla = $_POST['tabla'];
    $id = (int)$_POST['id'];
    $db->cambiarEstado($tabla, $id, 'Reservado');
    $_SESSION['flash'][] = ['type' => 'success', 'text' => "✅ {$tabla} reservado correctamente."];
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

// --- Obtener resultados ---
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
    <button class="nueva-box" onclick="window.location.href='nueva_pelicula.php'"><?=$lang_data["nueva_pelicula"]?></button>
    <button class="nueva-box" onclick="window.location.href='logout.php'"><?=$lang_data["cerrar_sesion"]?></button>
</div>

<div class="resultados">
    <?= $mensaje ?>

    <h2>🎬 <?= $lang_data['peliculas'] ?? 'Películas' ?></h2>
    <?php if(count($peliculas) > 0): ?>
        <?php foreach($peliculas as $p): ?>
            <?= $p->aHTML($lang_data) ?>
            <?php if($p->estado === 'Disponible'): ?>
                <form method="POST" style="margin-bottom:20px;">
                    <input type="hidden" name="tabla" value="Peliculas">
                    <input type="hidden" name="id" value="<?= $p->id ?>">
                    <button type="submit" name="reservar">Reservar</button>
                </form>
            <?php else: ?>
                <p>❌ Reservado</p>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>❌ No hay películas que cumplan los filtros.</p>
    <?php endif; ?>

    <h2>📚 <?= $lang_data['libros'] ?? 'Libros' ?></h2>
    <?php if(count($libros) > 0): ?>
        <?php foreach($libros as $l): ?>
            <?= $l->aHTML($lang_data) ?>
            <?php if($l->estado === 'Disponible'): ?>
                <form method="POST" style="margin-bottom:20px;">
                    <input type="hidden" name="tabla" value="Libros">
                    <input type="hidden" name="id" value="<?= $l->id ?>">
                    <button type="submit" name="reservar">Reservar</button>
                </form>
            <?php else: ?>
                <p>❌ Reservado</p>
            <?php endif; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <p>❌ No hay libros que cumplan los filtros.</p>
    <?php endif; ?>

    <p>✅ Has visitado el catálogo <strong><?= $_SESSION['visitas'] ?></strong> veces.</p>
</div>

</body>
</html>
