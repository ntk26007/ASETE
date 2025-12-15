<?php
session_start();
require_once "conexion.php";
require_once "DB.php";

$db = new DB($conexion);

$idItem = (int)($_POST['id_item'] ?? 0);
$tabla  = $_POST['tabla'] ?? null;

// Validar lo mínimo SIEMPRE
if (!$idItem || !$tabla) {
    $_SESSION['flash'][] = ['type'=>'error','text'=>'Datos incompletos.'];
    header("Location: catalogo.php");
    exit();
}

// Determinar columna correcta
$campo = ($tabla === "Libros") ? "IdLibro" : "IdPeliculas";

// ¿Está reservado?
$stmt = $conexion->prepare("SELECT * FROM Reservas WHERE $campo = ?");
$stmt->bind_param("i", $idItem);
$stmt->execute();
$reserva = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* =======================
   DEVOLVER (SIN CLIENTE)
   ======================= */
if (isset($_POST['devolver'])) {

    if ($reserva) {
        $stmt = $conexion->prepare("DELETE FROM Reservas WHERE $campo = ?");
        $stmt->bind_param("i", $idItem);
        $stmt->execute();
        $stmt->close();

        $db->cambiarEstado($tabla, $idItem, "Disponible");

        $_SESSION['flash'][] = [
            'type'=>'info',
            'text'=>'🔄 Artículo devuelto correctamente.'
        ];
    } else {
        $_SESSION['flash'][] = [
            'type'=>'error',
            'text'=>'❌ Este artículo no está reservado.'
        ];
    }

/* =======================
   RESERVAR (CON CLIENTE)
   ======================= */
} else {

    $idCliente = (int)($_POST['idCliente'] ?? 0);

    if (!$idCliente) {
        $_SESSION['flash'][] = ['type'=>'error','text'=>'Debes seleccionar un cliente.'];
        header("Location: catalogo.php");
        exit();
    }

    if ($reserva) {
        $_SESSION['flash'][] = [
            'type'=>'error',
            'text'=>'❌ Este artículo ya está reservado.'
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

// Volver al catálogo con filtros
$volver = $_SESSION['volver_catalogo'] ?? "catalogo.php";
header("Location: $volver");
exit();
?>