<?php
include 'idioma.php';
include 'Formatear.php';
include 'Peliculas.php';


?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Filtro de películas</title>
    <link rel="stylesheet" href="style.css">
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
    
    <!-- Contenedor del formulario de filtros -->
        <div class="container">
        <form action="catalogo.php" method="GET">

            <!-- Filtro por géneros -->
            <label><?= $lang_data['generos'] ?></label>
            <div class="checkbox-group">
                <!-- Cada checkbox tiene su valor original y su etiqueta traducida -->
                <label><input type="checkbox" name="genero[]" value="Drama"><?= $lang_data['drama'] ?></label>
                <label><input type="checkbox" name="genero[]" value="Ciencia ficción"><?= $lang_data['ciencia'] ?></label>
                <label><input type="checkbox" name="genero[]" value="Biografía"><?= $lang_data['biografia'] ?></label>
                <label><input type="checkbox" name="genero[]" value="Romance"><?= $lang_data['romance'] ?></label>
                <label><input type="checkbox" name="genero[]" value="Fantasía"><?= $lang_data['fantasia'] ?></label>
                <label><input type="checkbox" name="genero[]" value="Thriller"><?= $lang_data['thriller'] ?></label>
            </div>

            <!-- Filtro por título de película -->
            <label for="titulo"><?= $lang_data['titulo'] ?></label>
            <input type="text" name="titulo" id="titulo" placeholder="Ej: Inception">
        
            <!-- Filtro por año de estreno -->
            <label for="año"><?= $lang_data['año'] ?></label>
            <input type="number" name="año" id="año" placeholder="Ej: 2003">

            <!-- Filtro por director -->
            <label for="director"><?= $lang_data['director'] ?></label>
            <input type="text" name="director" id="director" placeholder="Ej: Burton">

            <!-- Filtro por actor -->
            <label for="actor"><?= $lang_data['actor'] ?></label>
            <input type="text" name="actor" id="actor" placeholder="Ej: Williams">

            <!-- Botón para enviar filtros, texto traducido -->
            <input type="submit" class="boton" value=<?= $lang_data['filtrar'] ?>>
        </form>
    </div>
</body>
</html>
