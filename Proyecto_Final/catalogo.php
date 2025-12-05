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

// Contador de visitas
if (!isset($_SESSION['visitas'])) $_SESSION['visitas'] = 0;
$visitas = &$_SESSION['visitas'];
Utils::incrementarVisitas($visitas);

// Inicializar DB
$db = new DB($conexion);

// --- Filtros ---
$filtros = [
    'titulo' => $_GET['titulo'] ?? '',
    'genero' => $_GET['genero'] ?? '',
    'año' => $_GET['año'] ?? '',
    'director' => $_GET['director'] ?? '',
    'actor' => $_GET['actor'] ?? '',
    'editorial' => $_GET['editorial'] ?? ''
];

// Datos
$peliculas = $db->getPeliculas($filtros);
$libros = $db->getLibros($filtros);

// Mensajes flash
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
.catalogo-container { display: flex; gap: 20px; align-items: flex-start; }
.catalogo-col { flex: 1; }
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
    <button class="nueva-box" onclick="window.location.href='mis_reservas.php'"><?= $lang_data["mis_reservas"] ?></button>
</div>

<div class="resultados">
    <?= $mensaje ?>

    <div class="catalogo-container">

    <!-- PELÍCULAS -->
    <div class="catalogo-col">
        <h2>🎬 <?= $lang_data['peliculas'] ?></h2>

        <?php if (count($peliculas) > 0): ?>
            <?php foreach ($peliculas as $p): ?>
                <?= $p->aHTML($lang_data) ?>

                <form action="reservar.php" method="POST">
                    <input type="hidden" name="tabla" value="Peliculas">
                    <input type="hidden" name="id_item" value="<?= $p->id ?>">
                    <?php if ($p->estado === "Disponible") : ?>
                        <button class="boton-reservar" type="submit" name="reservar"><?= $lang_data['reservar']; ?></button>
                    <?php else: ?>
                        <button class="boton-devolver" type="submit" name="devolver"><?= $lang_data['devolver']; ?></button>
                    <?php endif; ?>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>❌ <?= $lang_data['no_hay_peliculas'] ?? 'No hay películas.' ?></p>
        <?php endif; ?>
    </div>

    <!-- LIBROS -->
    <div class="catalogo-col">
        <h2>📚 <?= $lang_data['libros'] ?></h2>

        <?php if (count($libros) > 0): ?>
            <?php foreach ($libros as $l): ?>
                <?= $l->aHTML($lang_data) ?>

                <form action="reservar.php" method="POST">
                    <input type="hidden" name="tabla" value="Libros">
                    <input type="hidden" name="id_item" value="<?= $l->id ?>">
                    <?php if ($l->estado === "Disponible") : ?>
                        <button class="boton-reservar" type="submit" name="reservar"><?= $lang_data['reservar']; ?></button>
                    <?php else: ?>
                        <button class="boton-devolver" type="submit" name="devolver"><?= $lang_data['devolver']; ?></button>
                    <?php endif; ?>
                </form>
            <?php endforeach; ?>
        <?php else: ?>
            <p>❌ <?= $lang_data['no_hay_libros'] ?? 'No hay libros.' ?></p>
        <?php endif; ?>
    </div>

    </div>
    <p><?= $lang_data['visitas'] ?> <strong><?= $_SESSION['visitas'] ?></strong> veces.</p>
</div>
</body>
</html>
