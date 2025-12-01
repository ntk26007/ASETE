<?php

    session_start();

    require "internacionalizacion.php";

    if(!isset($_SESSION['usuario'])){
        header("Location: login.php");
    }

    require_once "peliculas.php";

    //Nos aseguramos de que la lista de películas exista en sesión, y la deserializamos a lista de objetos
    if(!isset($_SESSION["peliculas"])){
        $_SESSION["peliculas"] = unserialize($peliculas);
    }

    $mensaje = "";
    $error = false;

    //Recuperamos la película enviada en el POST desde el formulario
    $titulo = $_POST["titulo"] ?? "";
    $año = $_POST["año"] ?? "";
    $director = $_POST["director"] ?? "";
    $actores = $_POST["actores"] ?? "";
    $genero = $_POST["genero"] ?? "";

    if ($titulo && $año && $director && $genero) {
        $nueva = new Pelicula($titulo, $año, $director, $actores, $genero);

        $peliculas = unserialize($_SESSION["peliculas"]);
        $peliculas[] = $nueva;
        $_SESSION["peliculas"] = serialize($peliculas);
        $mensaje = "Película añadida correctamente.";

        //FALTA la parte para agregar serie o corto

    } else {
        $mensaje = "Todos los campos obligatorios deben estar completos.";
        $error = true;
    }
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Proyecto ASETE 1</title>
        <link rel="stylesheet" href="style/agregar_pelicula.css">
        <link rel="stylesheet" href="style/idioma.css">
    </head>
    <body>

    <?php include "caja-idiomas.html"; ?>

        <h1><?= $traducciones["add_new_film"] ?></h1>
        <p><?= $traducciones["user"] ?>: <?= $_SESSION['usuario'] ?> | <a href="catalogo.php"><?= $traducciones["back_to_catalog"] ?></a></p>

        <?php if($mensaje && $error): ?>
            <p class="mensaje error"><?= $mensaje ?></p>
        <?php elseif($mensaje): ?>
            <p class="mensaje"><?= $mensaje ?></p>
        <?php endif; ?>

        <form method="POST">
            <label for="titulo"><?= $traducciones["title"] ?>:</label>
            <input type="text" name="titulo" id="titulo" required>

            <label for="año"><?= $traducciones["year"] ?>:</label>
            <input type="number" name="año" id="año" required>

            <label for="director"><?= $traducciones["director"] ?>:</label>
            <input type="text" name="director" id="director" required>

            <label for="actores"><?= $traducciones["actors"] ?>:</label>
            <input type="text" name="actores" id="actores">

            <label for="genero"><?= $traducciones["genre"] ?>:</label>
            <select name="genero" id="genero" required>
                <option value="">Seleccionar</option>
                <option>Drama</option>
                <option>Romance</option>
                <option>Biografía</option>
                <option>Ciencia ficción</option>
                <option>Fantasía</option>
                <option>Animación</option>
                <option>Thriller</option>
            </select>

            <button type="submit"><?= $traducciones["save_film"] ?></button>
        </form>
    </body>
</html>
