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

// --- Tipos seleccionados en index.php ---
$tipos = $_GET['tipo'] ?? [];   // ["libros", "peliculas"]

// --- Filtros según lo seleccionado ---
$filtrosPeliculas = [
    'titulo'   => $_GET['titulo_pelicula'] ?? '',
    'genero'   => $_GET['genero_pelicula'] ?? '',
    'año'      => $_GET['año'] ?? '',
    'director' => $_GET['director'] ?? '',
    'actor'    => $_GET['actor'] ?? ''
];

$filtrosLibros = [
    'titulo'   => $_GET['titulo_libro'] ?? '',
    'genero'   => $_GET['genero_libro'] ?? '',
    'autor'    => $_GET['autor'] ?? '',
    'editorial'=> $_GET['editorial'] ?? ''
];

// Datos según filtros
$peliculas = in_array("peliculas", $tipos) ? $db->getPeliculas($filtrosPeliculas) : [];
$libros    = in_array("libros", $tipos)    ? $db->getLibros($filtrosLibros)     : [];

// Mensajes flash
$mensaje = '';
if (isset($_SESSION['flash'])) {
    foreach ($_SESSION['flash'] as $flash) {
        $mensaje .= "<p class='{$flash['type']}'>{$flash['text']}</p>";
    }
    unset($_SESSION['flash']);
}

// Construir URL para volver con filtros
$parametros = http_build_query($_GET);
$volver_url = "catalogo.php?" . $parametros;
$_SESSION['volver_catalogo'] = $volver_url;

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
    <button class="nueva-box" onclick="window.location.href='añadir.php'"><?= $lang_data["añadir"] ?></button>
    <button class="nueva-box" onclick="window.location.href='logout.php'"><?= $lang_data["cerrar_sesion"] ?></button>
    <button class="nueva-box" onclick="window.location.href='mis_reservas.php'"><?= $lang_data["mis_reservas"] ?></button>
</div>

<div class="resultados">
    <?= $mensaje ?>

    <div class="catalogo-container">

    <!-- ====================
            PELÍCULAS
    ===================== -->
    <?php if (in_array("peliculas", $tipos)): ?>
    <div class="catalogo-col">
        <h2>🎬 <?= $lang_data['peliculas'] ?></h2>

        <?php if (count($peliculas) > 0): ?>
            <?php foreach ($peliculas as $p): ?>
                <?= $p->aHTML($lang_data) ?>

                <form action="reservar.php" method="POST">
                    <input type="hidden" name="tabla" value="Peliculas">
                    <input type="hidden" name="id_item" value="<?= $p->id ?>">

                    <?php if ($p->estado === "Disponible") : ?>
                        <button class="boton-reservar" type="submit" name="reservar">
                            <?= $lang_data['reservar']; ?>
                        </button>
                    <?php else: ?>
                        <button class="boton-devolver" type="submit" name="devolver">
                            <?= $lang_data['devolver']; ?>
                        </button>
                    <?php endif; ?>
                </form>
            <?php endforeach; ?>

        <?php else: ?>
            <p>❌ <?= $lang_data['no_hay_peliculas'] ?? 'No hay películas que coincidan con los filtros.' ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <!-- ====================
              LIBROS
    ===================== -->
    <?php if (in_array("libros", $tipos)): ?>
    <div class="catalogo-col">
        <h2>📚 <?= $lang_data['libros'] ?></h2>

        <?php if (count($libros) > 0): ?>
            <?php foreach ($libros as $l): ?>
                <?= $l->aHTML($lang_data) ?>

                <form action="reservar.php" method="POST">
                    <input type="hidden" name="tabla" value="Libros">
                    <input type="hidden" name="id_item" value="<?= $l->id ?>">

                    <?php if ($l->estado === "Disponible") : ?>
                        <button class="boton-reservar" type="submit" name="reservar">
                            <?= $lang_data['reservar']; ?>
                        </button>
                    <?php else: ?>
                        <button class="boton-devolver" type="submit" name="devolver">
                            <?= $lang_data['devolver']; ?>
                        </button>
                    <?php endif; ?>
                </form>
            <?php endforeach; ?>

        <?php else: ?>
            <p>❌ <?= $lang_data['no_hay_libros'] ?? 'No hay libros que coincidan con los filtros.' ?></p>
        <?php endif; ?>

    </div>
    <?php endif; ?>

    </div>
    <br>
    <p><?= $lang_data['visitas'] ?> <strong><?= $_SESSION['visitas'] ?></strong> veces.</p>
</div>

</body>
</html>
