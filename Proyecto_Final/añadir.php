<?php
session_start();
include 'idioma.php';
require_once 'Peliculas.php';
require_once 'DB.php';
include 'conexion.php';

$db = new DB($conexion);

// Guardamos la ruta a la que volver (catálogo con filtros)
if (!isset($_SESSION['volver_catalogo'])) {
    $_SESSION['volver_catalogo'] = 'catalogo.php';
}

// Proteger página
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Valores por defecto
$valores = [
    'tipo' => 'pelicula',
    'titulo' => '',
    'año_pelicula' => '',
    'año_libro' => '',
    'director' => '',
    'actores' => '',
    'genero' => '',
    'autor_id' => '',
    'editorial' => '',
    'paginas' => '',
    'precio' => '',
    'tipo_adaptacion' => 'Película'
];

$errores = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Tipo
    $valores['tipo'] = $_POST['tipo'] ?? 'pelicula';

    // Valores generales
    $valores['titulo'] = trim($_POST['titulo'] ?? '');
    $valores['genero'] = trim($_POST['genero'] ?? '');

    // ==============================
    //     VALIDAR PELÍCULA
    // ==============================
    if ($valores['tipo'] === 'pelicula') {

        $valores['año_pelicula'] = trim($_POST['año_pelicula'] ?? '');
        $valores['director'] = trim($_POST['director'] ?? '');
        $valores['actores'] = trim($_POST['actores'] ?? '');

        if ($valores['titulo'] === '')  $errores[] = 'El título es obligatorio.';
        if ($valores['año_pelicula'] === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $valores['año_pelicula']))
            $errores[] = 'La fecha de estreno debe tener formato YYYY-MM-DD.';
        if ($valores['director'] === '') $errores[] = 'El director es obligatorio.';
        if ($valores['genero'] === '')  $errores[] = 'El género es obligatorio.';

        if (empty($errores)) {
            // Guardar película en la base de datos
            $db->insertarPelicula(
                $valores['titulo'],
                $valores['año_pelicula'], // fecha completa
                $valores['director'],
                $valores['actores'],
                $valores['genero'],
                $valores['tipo_adaptacion']
            );

            $_SESSION['flash'][] = ['type' => 'success', 'text' => "🎬 Película añadida correctamente."];
            header("Location: " . $_SESSION['volver_catalogo']);
            exit();
        }
    }

    // ==============================
    //         VALIDAR LIBRO
    // ==============================
    else {

        $valores['autor_id']  = trim($_POST['autor_id'] ?? '');
        $valores['editorial'] = trim($_POST['editorial'] ?? '');
        $valores['paginas']   = trim($_POST['paginas'] ?? '');
        $valores['año_libro'] = trim($_POST['año_libro'] ?? '');
        $valores['precio']    = trim($_POST['precio'] ?? '');

        if ($valores['titulo'] === '')  $errores[] = 'El título es obligatorio.';
        if ($valores['genero'] === '')  $errores[] = 'El género es obligatorio.';
        if (empty($valores['autor_id']) || intval($valores['autor_id']) <= 0)
            $errores[] = 'Debes seleccionar un autor.';

        if ($valores['paginas'] !== '' && !is_numeric($valores['paginas']))
            $errores[] = 'Páginas debe ser un número.';
        if ($valores['precio'] !== '' && !is_numeric($valores['precio']))
            $errores[] = 'Precio debe ser un número.';

        if (empty($errores)) {
            $db->insertarLibro(
                $valores['titulo'],
                $valores['genero'],
                intval($valores['autor_id']),
                $valores['editorial'],
                $valores['paginas'] === '' ? 0 : intval($valores['paginas']),
                $valores['año_libro'] === '' ? null : $valores['año_libro'],
                $valores['precio'] === '' ? 0 : intval($valores['precio'])
            );

            $_SESSION['flash'][] = ['type' => 'success', 'text' => "📚 Libro añadido correctamente."];
            header("Location: " . $_SESSION['volver_catalogo']);
            exit();
        }
    }

    // Enviar errores a pantalla
    if (!empty($errores)) {
        $_SESSION['flash'][] = ['type' => 'error', 'text' => implode("<br>", $errores)];
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= $lang_data['añadir'] ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1><?= $lang_data['añadir'] ?></h1>

<div class="idiomas">
    🌐 
    <a href="idioma.php?lang=es">Español</a> | 
    <a href="idioma.php?lang=en">English</a>
</div>

<div class="container">

    <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $f): ?>
            <p class="<?= $f['type'] ?>"><?= $f['text'] ?></p>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <form method="POST" action="añadir.php">

        <label><?= $lang_data['tipo'] ?> </label>
        <select name="tipo" onchange="mostrarCampos()" id="tipo-select">
            <option value="pelicula" <?= $valores['tipo']=='pelicula'?'selected':'' ?>>Película</option>
            <option value="libro" <?= $valores['tipo']=='libro'?'selected':'' ?>>Libro</option>
        </select>

        <label><?= $lang_data['titulo'] ?> </label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($valores['titulo']) ?>">

        <!-- PELÍCULA -->
        <div id="campos-pelicula">
            <div class="campo">
                <label><?= $lang_data['fecha_estreno'] ?> </label>
                <input type="date" name="año_pelicula"
                       value="<?= isset($valores['año_pelicula']) ? htmlspecialchars($valores['año_pelicula']) : '' ?>">
            </div>
            <br>
            <div class="campo">
                <label><?= $lang_data['director'] ?> </label>
                <input type="text" name="director"
                       value="<?= isset($valores['director']) ? htmlspecialchars($valores['director']) : '' ?>">
            </div>
            <br>
            <div class="campo">
                <label><?= $lang_data['actores'] ?> </label>
                <input type="text" name="actores"
                       value="<?= isset($valores['actores']) ? htmlspecialchars($valores['actores']) : '' ?>">
            </div>
            <br>
            <div class="campo">
                <label><?= $lang_data['genero'] ?> </label>
                <input type="text" name="genero" value="<?= htmlspecialchars($valores['genero']) ?>">
            </div>
            <div class="campo">
                <label><?= $lang_data['tipo_adaptacion'] ?> </label>
                <select name="tipo_adaptacion">
                    <option value="pelicula" <?= ($valores['tipo_adaptacion'] ?? '') == 'pelicula' ? 'selected' : '' ?>>Película</option>
                    <option value="serie" <?= ($valores['tipo_adaptacion'] ?? '') == 'serie' ? 'selected' : '' ?>>Serie</option>
                    <option value="cortometraje" <?= ($valores['tipo_adaptacion'] ?? '') == 'cortometraje' ? 'selected' : '' ?>>Cortometraje</option>
                </select>
            </div>
        </div>

        <!-- LIBRO -->
        <div id="campos-libro">
            <div class="campo">
                <label><?= $lang_data['autor'] ?> </label>
                <?php
                $autores = $conexion->query("SELECT ID, NOMBRE FROM Autores ORDER BY NOMBRE");
                ?>
                <select name="autor_id">
                    <option value="0"><?= $lang_data['seleciona_autor'] ?> </option>
                    <?php while($a = $autores->fetch_assoc()): ?>
                        <option value="<?= $a['ID'] ?>" <?= $valores['autor_id']==$a['ID'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($a['NOMBRE']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <br>
            <div class="campo">
                <label><?= $lang_data['editorial'] ?> </label>
                <input type="text" name="editorial" value="<?= htmlspecialchars($valores['editorial']) ?>">
            </div>
            <br>
            <div class="campo">
                <label><?= $lang_data['paginas'] ?> </label>
                <input type="number" name="paginas" value="<?= htmlspecialchars($valores['paginas']) ?>">
            </div>
            <br>
            <div class="campo">
                <label><?= $lang_data['año_libro'] ?> </label>
                <input type="date" name="año_libro" value="<?= htmlspecialchars($valores['año_libro']) ?>">
            </div>
            <br>
            <div class="campo">
                <label><?= $lang_data['precio'] ?> </label>
                <input type="number" name="precio" value="<?= htmlspecialchars($valores['precio']) ?>">
            </div>
        </div>

        <input type="submit" value="<?= $lang_data['añadir'] ?>">
    </form>

    <!-- VOLVER AL CATÁLOGO CON FILTROS RESTAURADOS -->
    <button class="boton-flecha" onclick="window.location.href='<?= $_SESSION['volver_catalogo'] ?>'"></button>
</div>

<script>
function mostrarCampos() {
    const tipo = document.getElementById("tipo-select").value;
    document.getElementById("campos-pelicula").style.display = (tipo === "pelicula") ? "block" : "none";
    document.getElementById("campos-libro").style.display = (tipo === "libro") ? "block" : "none";
}
mostrarCampos();
</script>

</body>
</html>
