<?php
    $servidor = "bbdd";
    $usuario = "root";
    $contrasena = "bbdd";
    $nombre_de_la_bbdd = "proyecto"; //Nombre de la Base de Datos en PHPMyAdmin dentro de Docker
    
    $conexion = new mysqli($servidor, $usuario, $contrasena, $nombre_de_la_bbdd);

     if($conexion->connect_error)
        echo "Conexión error:" . $conexion->connect_error;
    /*else   
        echo "Conectado sin error";
*/
    //Selecionar base de datos
    $conexion->select_db($nombre_de_la_bbdd);
    //establecer codificacion de caracteres
    $conexion->set_charset("utf8");

    //Crear tabla de usuarios si no existe
    $crearTabla = "CREATE TABLE IF NOT EXISTS usuarios (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL
    )";
    $conexion->query($crearTabla);
   
?>