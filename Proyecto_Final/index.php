<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'idioma.php';
include 'Formatear.php'; // asegúrate del nombre en minúsculas
include 'Peliculas.php'; // idem
include 'conexion.php';

// Obtener géneros dinámicos desde la BD
$generosLibros = $conexion->query("SELECT DISTINCT Genero FROM Libros ORDER BY Genero")->fetch_all(MYSQLI_ASSOC);
$generosPeliculas = $conexion->query("SELECT DISTINCT Genero FROM Peliculas ORDER BY Genero")->fetch_all(MYSQLI_ASSOC);
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Filtro de películas</title>
    <link rel="stylesheet" href="style.css">

    <style>
        .oculto { display: none; }
    </style>

    <script>
        function toggleFiltros() {
            const chkLibros = document.getElementById("chkLibros").checked;
            const chkPeliculas = document.getElementById("chkPeliculas").checked;

            document.getElementById("filtrosLibros").style.display =
                chkLibros ? "block" : "none";

            document.getElementById("filtrosPeliculas").style.display =
                chkPeliculas ? "block" : "none";
        }
    </script>
</head>
<body>
    <h1><?= $lang_data['titulo_catalogo'] ?></h1>
    <!-- Mensaje de bienvenida con el usuario logueado y enlace para cerrar sesión -->
    <p><?= $lang_data['bienvenido'] ?> 
    <?= htmlspecialchars($_SESSION['usuario']) ?> 
    <br>
    <a href="logout.php" style="color: #FFD700;">
    <?= $lang_data['cerrar_sesion'] ?></a></p>

    <!-- Sección para cambiar el idioma de la página -->
    <div class="idiomas">
        🌐 
        <a href="idioma.php?lang=es">Español</a> | 
        <a href="idioma.php?lang=en">English</a>
    </div>

<div class="container">

<form action="catalogo.php" method="GET">

    <!-- Selección de tipo -->
    <h2><?= $lang_data['ver'] ?></h2>
    <label><input type="checkbox" id="chkLibros" name="tipo[]" value="libros" onclick="toggleFiltros()"> <?= $lang_data['Libros'] ?></label>
    <label><input type="checkbox" id="chkPeliculas" name="tipo[]" value="peliculas" onclick="toggleFiltros()"> <?= $lang_data['Peliculas'] ?></label>

    <!-- ======================
         FILTROS PARA LIBROS
    ======================= -->
    <div id="filtrosLibros" class="oculto">
        <h3><?= $lang_data['filtras_libros'] ?> </h3>
        <br>
        <div class="campo">
            <label>Género</label>
            <select name="genero_libro">
                <option value=""><?= $lang_data['todos'] ?> </option>
                <?php foreach ($generosLibros as $g): ?>
                    <option value="<?= $g['Genero'] ?>"><?= $g['Genero'] ?></option>
                <?php endforeach; ?>
             </select>
        </div>
        <br>
        <div class="campo">
            <label><?= $lang_data['titulo'] ?> </label>
            <input type="text" name="titulo_libro">
        </div>
        <br>
        <div class="campo">
            <label><?= $lang_data['autor'] ?> </label>
            <input type="text" name="autor">
        </div>
        <br>
        <div class="campo">
            <label><?= $lang_data['año'] ?> </label>
            <input type="text" name="año">
        </div>
    </div>

    <!-- ======================
         FILTROS PARA PELÍCULAS
    ======================= -->
    <div id="filtrosPeliculas" class="oculto">
        <h3><?= $lang_data['filtrar_peliculas'] ?> </h3>
        <br>
        <div class="campo">
            <label>Género</label>
            <select name="genero_pelicula">
                <option value=""><?= $lang_data['todos'] ?> </option>
                <?php foreach ($generosPeliculas as $g): ?>
                    <option value="<?= $g['Genero'] ?>"><?= $g['Genero'] ?></option>
                 <?php endforeach; ?>
            </select>
        </div>
        <br>
        <div class="campo">
            <label><?= $lang_data['titulo'] ?> </label>
            <input type="text" name="titulo_pelicula">
        </div>
        <br>
        <div class="campo">
            <label><?= $lang_data['director'] ?> </label>
            <input type="text" name="director">
        </div>
        <br>
        <div class="campo">
            <label><?= $lang_data['actor'] ?> </label>
            <input type="text" name="actor">
        </div>
        <br>
        <div class="campo">
            <label><?= $lang_data['año'] ?> </label>
            <input type="text" name="año">
        </div>
    </div>

    <hr>

    <input type="submit" class="boton" value="<?= $lang_data['filtrar'] ?>">

</form>
    </div>
</body>
</html>
