<?php
    session_start();

    //Añado la dependenci al fichero donde gestiono la internacionalización
    //Hacerlo así es IMPORTANTE par que no estéis repitiendo código todo el rato (ayuda a modulizar el proyecto)
    require "internacionalizacion.php";

    //Creamos un array de usuarios que se podrán loguear en la aplicación
    //Esta parte la tendréis que sustituir por cosnultas a BBDD para comprobar que el usuario está registrado
    $usuarios = [
        "admin" => "1234",
        "gonzalo" => "gonzalo"
    ];

    //Creamos una variable para almacenar el error si lo hubiera
    $error = "";

    //Recuperamos la información enviada por método POST
    $usuario = $_POST["usuario"] ?? "";
    $contrasena = $_POST["contrasena"] ?? "";

    if($_SERVER["REQUEST_METHOD"] == "POST") { //Para que sólo eejcute este IF si recibe algo por POST
        //Si el usuario existe en el array y su valor coincide con la contraseña introducida... entra
        if(isset($usuarios[$usuario]) && $usuarios[$usuario] == $contrasena){
            $_SESSION["usuario"] = $usuario;
            header("Location: catalogo.php");
        } else {
            $error = "Usuario o contraseña incorrectos";
        }
    }
?>
<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Proyecto ASETE 1</title>
        <link rel="stylesheet" href="style/login.css">
        <link rel="stylesheet" href="style/idioma.css">
    </head>
    <body>
        <?php include "caja-idiomas.html"; ?>
        <div class="caja-login">
            <h1><?=$traducciones["login"]?></h1>

            <?php if($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post">
                <label for="usuario"><?=$traducciones["user"]?></label>
                <input type="text" name="usuario" id="usuario" required>

                <label for="clave"><?=$traducciones["pass"]?></label>
                <input type="password" name="contrasena" id="clave" required>

                <button type="submit"><?=$traducciones["enter"]?></button>
            </form>
        </div>
    </body>
</html>