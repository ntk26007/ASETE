<?php
session_start();
$_SESSION = [];

//Esa condición sirve para cerrar la sesión de forma segura
if (ini_get("session.use_cookies")) { //Comprueba si PHP está usando cookies para manejar las sesiones.
    $params = session_get_cookie_params(); //session.use_cookies suele ser 1 (true) 
    // Si es true, significa que la sesión se guarda en una cookie
    setcookie(session_name(), '', time() - 42000, //fecha en el pasado → la cookie expira
    //el session_name es el nombre de la cookie que almacena el id de sesión(por defecto PHPSESSID)
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
header('Location: login.php');
exit();
?>
