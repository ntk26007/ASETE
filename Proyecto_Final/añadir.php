<?php
session_start();
include 'idioma.php';
require_once 'Peliculas.php';
require_once 'DB.php';
include 'conexion.php';

$db = new DB($conexion);

// Protege la página: si no hay usuario logueado, redirige al login
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Inicializar lista películas en la sesión si no existe
if (!isset($_SESSION['peliculas'])) {
    $_SESSION['peliculas'] = [];
}

// Valores iniciales
$valores = [
    'tipo' => 'pelicula',
    'titulo' => '',
    'año' => '',
    'director' => '',
    'actores' => '',
    'genero' => '',
    'autor' => '',
    'editorial' => ''
];

// Array para almacenar errores de validación
$errores = [];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Recoger valores
    $valores['tipo'] = $_POST['tipo'] ?? 'pelicula';
    $valores['titulo'] = trim($_POST['titulo'] ?? '');
    $valores['genero'] = trim($_POST['genero'] ?? '');

    // Película
    if ($valores['tipo'] === 'pelicula') {
        $valores['año'] = trim($_POST['año'] ?? '');
        $valores['director'] = trim($_POST['director'] ?? '');
        $valores['actores'] = trim($_POST['actores'] ?? '');

        if ($valores['titulo'] === '')  $errores[] = 'El título es obligatorio.';
        if ($valores['año'] === '' || !is_numeric($valores['año'])) $errores[] = 'El año debe ser un número.';
        if ($valores['director'] === '') $errores[] = 'El director es obligatorio.';
        if ($valores['genero'] === '')  $errores[] = 'El género es obligatorio.';

        // Si todo ok → guardar en BD
        if (empty($errores)) {
            $db->insertarPelicula(
                $valores['titulo'],
                intval($valores['año']),
                $valores['director'],
                $valores['actores'],
                $valores['genero']
            );

            $_SESSION['flash'][] = ['type' => 'success', 'text' => "🎬 Película añadida correctamente."];
            header('Location: catalogo.php?tipo[]=peliculas');
            exit();
        }

    } 
    // Libro
    else {
        $valores['autor_id']  = trim($_POST['autor_id'] ?? '');
        $valores['editorial'] = trim($_POST['editorial'] ?? '');
        $valores['paginas']   = trim($_POST['paginas'] ?? '');
        $valores['año']       = trim($_POST['año'] ?? ''); // formato YYYY-MM-DD o '' 
        $valores['precio']    = trim($_POST['precio'] ?? '');

        if ($valores['titulo'] === '')  $errores[] = 'El título es obligatorio.';
        if ($valores['genero'] === '')  $errores[] = 'El género es obligatorio.';
        if (empty($valores['autor_id']) || intval($valores['autor_id']) <= 0) $errores[] = 'Debes seleccionar un autor.';

        // Validaciones opcionales
        if ($valores['paginas'] !== '' && !is_numeric($valores['paginas'])) $errores[] = 'Páginas debe ser un número.';
        if ($valores['precio'] !== ''  && !is_numeric($valores['precio']))  $errores[] = 'Precio debe ser un número.';

        // Preparar valores para la BD (tipos)
        $autor_id = intval($valores['autor_id'] ?? 0);
        $paginas  = ($valores['paginas'] === '') ? 0 : intval($valores['paginas']);
        $precio   = ($valores['precio'] === '') ? 0 : intval($valores['precio']);
        $año_raw  = $valores['año']; // '' o 'YYYY-MM-DD'

        // Si no hay errores → insertar
        if (empty($errores)) {
            $db->insertarLibro(
                $valores['titulo'],
                $valores['genero'],
                $autor_id,
                $valores['editorial'],
                $paginas,
                $año_raw === '' ? null : $año_raw,
                $precio
             );

        $_SESSION['flash'][] = ['type' => 'success', 'text' => "📚 Libro añadido correctamente."];
        header('Location: catalogo.php?tipo[]=libros');
        exit();
}
    }

    // Si hay errores
    $_SESSION['flash'][] = ['type' => 'error', 'text' => implode('<br>', $errores)];
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

    <!-- Selector de idioma -->
    <div class="idiomas">
        🌐 
        <a href="idioma.php?lang=es">Español</a> | 
        <a href="idioma.php?lang=en">English</a>
    </div>

    <div class="container">
        <!-- Formulario para agregar nueva película -->
        <?php if (isset($_SESSION['flash'])): ?>
        <?php foreach ($_SESSION['flash'] as $f): ?>
            <p class="<?= $f['type'] ?>"><?= $f['text'] ?></p>
        <?php endforeach; ?>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>

    <form method="POST" action="añadir.php">

        <!-- Tipo de contenido -->
        <label>Tipo</label>
        <select name="tipo" onchange="mostrarCampos()" id="tipo-select">
            <option value="pelicula" <?= $valores['tipo']=='pelicula'?'selected':'' ?>>Película</option>
            <option value="libro" <?= $valores['tipo']=='libro'?'selected':'' ?>>Libro</option>
        </select>

        <!-- Título -->
        <label>Título</label>
        <input type="text" name="titulo" value="<?= htmlspecialchars($valores['titulo']) ?>">

        <!-- Película -->
        <div id="campos-pelicula">
            <div class="campo">
                <label>Año</label>
                <input type="text" name="año" value="<?= htmlspecialchars($valores['año']) ?>">
            </div>
            <br>
            <div class="campo">
                <label>Director</label>
                <input type="text" name="director" value="<?= htmlspecialchars($valores['director']) ?>">
            </div>
            <br>
            <div class="campo">
                <label>Actores</label>
                <input type="text" name="actores" value="<?= htmlspecialchars($valores['actores']) ?>">
            </div>
            <br>
        </div>

        <!-- Libro -->
        <div id="campos-libro">
            <label>Autor</label>
            <?php
            // Cargar autores para el select
            $autores = $conexion->query("SELECT ID, NOMBRE FROM Autores ORDER BY NOMBRE");
            ?>
            <select name="autor_id">
                <option value="0">-- Selecciona autor --</option>
                <?php while($a = $autores->fetch_assoc()): ?>
                    <option value="<?= $a['ID'] ?>" <?= ($valores['autor_id'] ?? '') == $a['ID'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($a['NOMBRE']) ?>
                    </option>
                <?php endwhile; ?>
            </select>

            <label>Editorial</label>
            <input type="text" name="editorial" value="<?= htmlspecialchars($valores['editorial']) ?>">

            <label>Páginas</label>
            <input type="number" name="paginas" min="0" value="<?= htmlspecialchars($valores['paginas'] ?? '') ?>">

            <label>Año (fecha)</label>
            <!-- Si prefieres solo año, cambiar a type="number" y en BD usar YEAR -->
            <input type="date" name="año" value="<?= htmlspecialchars($valores['año'] ?? '') ?>">

            <label>Precio</label>
            <input type="number" name="precio" min="0" step="1" value="<?= htmlspecialchars($valores['precio'] ?? '') ?>">
        </div>


        <!-- Género -->
        <label>Género</label>
        <input type="text" name="genero" value="<?= htmlspecialchars($valores['genero']) ?>">



        <input type="submit" value="<?= $lang_data['añadir'] ?>">
    </form>

    <button class="boton-flecha" onclick="window.location.href='<?= $_SESSION['volver_catalogo'] ?? 'catalogo.php' ?>'"></button>
</div>

<script>
// Mostrar campos según tipo
function mostrarCampos() {
    const tipo = document.getElementById("tipo-select").value;
    document.getElementById("campos-pelicula").style.display = (tipo === "pelicula") ? "block" : "none";
    document.getElementById("campos-libro").style.display   = (tipo === "libro") ? "block" : "none";
}

mostrarCampos(); // Ejecutar al cargar la página
</script>

</body>
</html>