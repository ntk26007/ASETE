<?php
session_start();
require_once "conexion.php";
require_once "DB.php";

if (!isset($_SESSION['idCliente'])) {
    header("Location: login.php");
    exit();
}

$db = new DB($conexion);

$idCliente = (int)$_SESSION['idCliente'];
$idItem    = (int)($_POST['id_item'] ?? 0);
$tabla     = $_POST['tabla'] ?? null;

if (!$idItem || !$tabla) {
    $_SESSION['flash'][] = ['type'=>'error','text'=>'Datos incompletos.'];
    header("Location: catalogo.php");
    exit();
}

// Determinar columna correcta
$campo = ($tabla === "Libros") ? "IdLibro" : "IdPeliculas";

/* =======================
   COMPROBAR RESERVA DEL CLIENTE
   ======================= */
$stmt = $conexion->prepare(
    "SELECT * FROM Reservas 
     WHERE $campo = ? AND IdCliente = ?"
);
$stmt->bind_param("ii", $idItem, $idCliente);
$stmt->execute();
$reserva = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* =======================
   DEVOLVER
   ======================= */
if (isset($_POST['devolver'])) {

    if (!$reserva) {
        $_SESSION['flash'][] = [
            'type'=>'error',
            'text'=>'❌ No puedes devolver un artículo que no has reservado.'
        ];
    } else {

        $stmt = $conexion->prepare(
            "DELETE FROM Reservas 
             WHERE $campo = ? AND IdCliente = ?"
        );
        $stmt->bind_param("ii", $idItem, $idCliente);
        $stmt->execute();
        $stmt->close();

        $db->cambiarEstado($tabla, $idItem, "Disponible");

        $_SESSION['flash'][] = [
            'type'=>'info',
            'text'=>'🔄 Artículo devuelto correctamente.'
        ];
    }

/* =======================
   RESERVAR
   ======================= */
} else {

    if ($reserva) {
        $_SESSION['flash'][] = [
            'type'=>'error',
            'text'=>'❌ Ya tienes este artículo reservado.'
        ];
    } else {

        if ($tabla === "Libros") {
            $stmt = $conexion->prepare(
                "INSERT INTO Reservas (IdCliente, IdLibro, IdPeliculas, Fecha_Reserva)
                 VALUES (?, ?, NULL, NOW())"
            );
        } else {
            $stmt = $conexion->prepare(
                "INSERT INTO Reservas (IdCliente, IdLibro, IdPeliculas, Fecha_Reserva)
                 VALUES (?, NULL, ?, NOW())"
            );
        }

        $stmt->bind_param("ii", $idCliente, $idItem);
        $stmt->execute();
        $stmt->close();

        $db->cambiarEstado($tabla, $idItem, "Reservado");

        $_SESSION['flash'][] = [
            'type'=>'success',
            'text'=>'✅ Reserva realizada correctamente.'
        ];
    }
}

// Volver al catálogo
$volver = $_SESSION['volver_catalogo'] ?? "catalogo.php";
header("Location: $volver");
exit();
?>
