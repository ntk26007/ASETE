<?php
// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

//si el usuario ha seleccionado un idioma
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];

}

// Cambio de idioma
if (isset($_GET['lang'])) {
    $_SESSION['lang'] = $_GET['lang'];

    // Redirigir correctamente incluso en login.php
    $ref = $_SERVER['HTTP_REFERER'] ?? 'login.php';
    header("Location: " . $ref);
    exit();
}

// Idioma por defecto
if (!isset($_SESSION['lang'])) {
    $_SESSION['lang'] = 'es';
}

// Incluir archivo de idioma
$lang = $_SESSION['lang'];
include "lang/$lang.php";
?>
