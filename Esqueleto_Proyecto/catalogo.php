<?php

    session_start();

    require "internacionalizacion.php";

    //Si no hay usuario logueado (el que sea, nos da igual con que sea uno), lo redirijo al login
    if(!isset($_SESSION["usuario"])){
        header("Location: login.php");
        exit;
    }

    //Inicaializamos los filtros
    $genero = "";
    $año = "";
    $director = "";
    //Capturar filtros enviados por GET
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        //Es el operador de fusión de null en PHP (desde la versión 7).
        $genero = $_GET["genero"] ?? "";
        $año = $_GET["año"] ?? "";
        $director = $_GET["director"] ?? "";

        //Guardar en sesión para que persistan
        $_SESSION["genero"] = $genero;
        $_SESSION["año"] = $año;
        $_SESSION["director"] = $director;
    } else {
        //Si no hay GET, tomar los filtros almacenados en sesión
        $genero = $_SESSION["genero"] ?? "";
        $año = $_SESSION["año"] ?? "";
        $director = $_SESSION["director"] ?? "";
    }

    require_once "peliculas.php";

    //Si no hay películas dentro de la sesión, las gaurdamos en ella
    if(!isset($_SESSION["peliculas"])){
        $_SESSION["peliculas"] = serialize($peliculas);
    }
    //Parece redundante, pero así cuando añdo una nueva película las tengo todas para mostrar
    $peliculas = unserialize($_SESSION["peliculas"]);

?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Proyecto ASETE 1</title>
        <link rel="stylesheet" href="style/catalogo.css">
        <link rel="stylesheet" href="style/idioma.css">
    </head>
    <body>

        <?php include "caja-idiomas.html"; ?>
        <h1><?= $traducciones["catalog"] ?></h1>
        <a href="filtro.php"><?= $traducciones["filter"] ?></a>
        <a href="agregar_peliculas.php" style="color: green;"><?= $traducciones["new_film"] ?></a>
        <p><?= $traducciones["user"] ?>: <?php echo $_SESSION['usuario']; ?> | <a href="logout.php"><?= $traducciones["logout"] ?></p>

        <table>
            <tr>
                <th><?= $traducciones["title"] ?></th>
                <th><?= $traducciones["year"] ?></th>
                <th><?= $traducciones["director"] ?></th>
                <th><?= $traducciones["actors"] ?></th>
                <th><?= $traducciones["genre"] ?></th>
                <th><?= $traducciones["seassons"] ?></th>
                <th><?= $traducciones["duration"] ?></th>
            </tr>

            <?php foreach ($peliculas as $pelicula): ?>
                <?php if(
                         ($pelicula->año == $año || $año == "") && 
                         ($pelicula->genero == $genero || $genero == "") && 
                         (stripos($pelicula->director, $director) != false || $director == "")
                        ): ?>
                    <tr>
                        <!--Está habilitado por defecto desde PHP 5.4, y sí se recomienda para imprimir variables 
                        de forma rápida: Es equivalente a  ?php echo ...  -->
                        <td><?= $pelicula->titulo ?></td>
                        <td><?= $pelicula->año ?></td>
                        <td><?= $pelicula->director ?></td>
                        <td><?= $pelicula->actores ?></td>
                        <td><?= $pelicula->genero ?></td>
                        <td>
                            <?php if($pelicula instanceof Serie): ?>
                            <?= $pelicula->n_temporadas ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($pelicula instanceof Corto): ?>
                            <?= $pelicula->duracion ?>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <?php $contador_peliculas = ($contador_peliculas ?? 0) + 1; ?>

                <?php endif; ?>
                <?php //echo $pelicula->mostrarPelicula(); ?>
                <?php //echo $pelicula->toJSON(); ?>

            <?php endforeach; ?>

            <?php if(empty($contador_peliculas)): ?>

                <tr>
                    <td colspan="5">No se ha encontrado ninguna película que coincida con la selección</td>
                </tr>

            <?php endif; ?>

        </table>

    </body>
</html>