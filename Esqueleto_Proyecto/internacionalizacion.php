<?php
    //NO hace falta hacer el session_start porque este fichero se inlcuye en otros ficheros después de hacerlo allí

    //Por defecto, intento recoger lo que haya en sesión (si lo hay) para guardarlo
    $lang = $_SESSION["idioma"] ?? "es";

    //Despues, ya evaluo si viene algo en el GET (sólo si hay petición GET)
    if($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["idioma"])){
        $lang = $_GET["idioma"] ?? "es";
    }

    //Construir archivo
    $file = "lang/$lang.php";

    //Cargar archivo del idioma
    if (file_exists($file)) {
        require $file;
    } else {
        require "lang/es.php";
    }
	
?>