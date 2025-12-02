<?php
session_start();
require_once 'idioma.php';
session_unset();
session_destroy();
header('Location: login.php');
exit();
?>