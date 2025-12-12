<?php
session_start();

if (!isset($_SESSION['idCliente'])) {
    header('Location: login.php');
    exit();
}

require_once "conexion.php";
require_once "Peliculas.php";
require_once "DB.php";

$db = new DB($conexion);

// Obtener datos del formulario
$idCliente = (int)$_SESSION['idCliente'];
$id_item   = (int)($_POST['id_item'] ?? 0);
$tabla     = $_POST['tabla'] ?? null;

// Validar datos
if (!$id_item || !$tabla) {
    $_SESSION['flash'][] = ['type' => 'error', 'text' => 'Datos incompletos.'];
    header("Location: catalogo.php");
    exit();
}

// Obtener item
$item = $db->getItemPorId($tabla, $id_item);
if (!$item) {
    $_SESSION['flash'][] = ['type' => 'error', 'text' => 'Item no encontrado.'];
    header("Location: catalogo.php");
    exit();
}

// RESERVAR
if (isset($_POST['reservar']) && $item->estado === "Disponible") {
    $stmt = $conexion->prepare(
        "INSERT INTO Reservas (IdCliente, IdLibro, Fecha_Reserva) VALUES (?, ?, NOW())"
    );
    $stmt->bind_param("ii", $idCliente, $id_item);
    if ($stmt->execute()) {
        $db->cambiarEstado($tabla, $id_item, "Reservado");
        $_SESSION['flash'][] = ['type'=>'success', 'text'=>'✅ Reserva realizada correctamente.'];
    } else {
        $_SESSION['flash'][] = ['type'=>'error', 'text'=>'❌ Error al realizar la reserva.'];
    }
    $stmt->close();
}

// DEVOLVER
if (isset($_POST['devolver']) && $item->estado === "Reservado") {
    $stmt = $conexion->prepare(
        "DELETE FROM Reservas WHERE IdCliente = ? AND IdLibro = ?"
    );
    $stmt->bind_param("ii", $idCliente, $id_item);
    if ($stmt->execute()) {
        $db->cambiarEstado($tabla, $id_item, "Disponible");
        $_SESSION['flash'][] = ['type'=>'info', 'text'=>'🔄 Artículo devuelto correctamente.'];
    } else {
        $_SESSION['flash'][] = ['type'=>'error', 'text'=>'❌ Error al devolver el artículo.'];
    }
    $stmt->close();
}

// Redirigir a Mis Reservas para ver el resultado inmediato
header("Location: mis_reservas.php");
exit();
?>
