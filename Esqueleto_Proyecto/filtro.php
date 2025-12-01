<?php
    session_start();

    require "internacionalizacion.php";

    //Recupero los filtros almacenados en sesión si los hubiera
    $genero = $_SESSION["genero"] ?? "";
    $año = $_SESSION["año"] ?? "";
    $director = $_SESSION["director"] ?? "";

?>


<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Proyecto ASETE 1</title>
  <link rel="stylesheet" href="style/filtro.css">
  <link rel="stylesheet" href="style/idioma.css">
</head>
<body>

  <?php include "caja-idiomas.html"; ?>

  <h1>Filtrar películas</h1>

  <form method="get" action="catalogo.php">
    <label for="genero"><?= $traducciones["genre"] ?>:</label>
    <select name="genero" id="genero">
      <option value="" <?= $genero == "" ? 'selected' : "" ?>>Todos</option>
      <option value="Biografía" <?= $genero == "Biografía" ? "selected" : "" ?>>Biografía</option>
      <option value="Ciencia ficción" <?= $genero == "Ciencia ficción" ? "selected" : "" ?>>Ciencia ficción</option>
      <option value="Romance" <?= $genero == "Romance" ? "selected" : "" ?>>Romance</option>
      <option value="Drama" <?= $genero == "Drama" ? "selected" : "" ?>>Drama</option>
      <option value="Deporte" <?= $genero == "Deporte" ? "selected" : "" ?>>Deporte</option>
      <option value="Fantasía" <?= $genero == "Fantasía" ? "selected" : "" ?>>Fantasía</option>
      <option value="Animación" <?= $genero == "Animación" ? "selected" : "" ?>>Animación</option>
      <option value="Misterio" <?= $genero == "Misterio" ? "selected" : "" ?>>Misterio</option>
      <option value="Thriller" <?= $genero == "Thriller" ? "selected" : "" ?>>Thriller</option>
    </select>

    <label for="año"><?= $traducciones["year"] ?>:</label>
    <input type="number" name="año" id="año" placeholder="Ej: 2000" value="<?= $año ?>">

    <label for="director"><?= $traducciones["director"] ?>:</label>
    <input type="text" name="director" id="director" placeholder="Ej: Burton" value="<?= $director ?>">

    <button type="submit"><?= $traducciones["filter"] ?></button>
  </form>

</body>
</html>
