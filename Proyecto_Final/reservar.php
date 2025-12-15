<?php
// Inicia la sesión para poder usar variables de sesión (usuario logueado, mensajes flash, etc.)
session_start();

// Incluye el archivo de conexión a la base de datos
require_once "conexion.php";

// Incluye la clase DB que gestiona operaciones sobre la base de datos
require_once "DB.php";

// Si el cliente no está autenticado, se redirige al login
if (!isset($_SESSION['idCliente'])) {
    header("Location: login.php");
    exit();
}

// Se crea el objeto de acceso a la base de datos
$db = new DB($conexion);

// Se obtiene el ID del cliente desde la sesión (convertido a entero por seguridad)
$idCliente = (int)$_SESSION['idCliente'];

// Se obtiene el ID del libro o película enviado por POST
$idItem = (int)($_POST['id_item'] ?? 0);

// Se obtiene la tabla origen (Libros o Peliculas)
$tabla = $_POST['tabla'] ?? null;

// Validación básica: si faltan datos obligatorios, se cancela la operación
if (!$idItem || !$tabla) {
    $_SESSION['flash'][] = [
        'type' => 'error',
        'text' => 'Datos incompletos.'
    ];
    header("Location: catalogo.php");
    exit();
}

// Según el tipo de artículo, se determina qué columna usar en la tabla Reservas
// - Libros  -> IdLibro
// - Películas -> IdPeliculas
$campo = ($tabla === "Libros") ? "IdLibro" : "IdPeliculas";

/* ==================================================
   COMPROBAR SI EL CLIENTE YA TIENE ESTE ARTÍCULO
   ================================================== */

// Se comprueba si existe una reserva del cliente para este libro o película
$stmt = $conexion->prepare(
    "SELECT * FROM Reservas 
     WHERE $campo = ? AND IdCliente = ?"
);
$stmt->bind_param("ii", $idItem, $idCliente);
$stmt->execute();

// Se obtiene la reserva (si existe)
$reserva = $stmt->get_result()->fetch_assoc();
$stmt->close();

/* ==================================================
   DEVOLVER ARTÍCULO
   ================================================== */
if (isset($_POST['devolver'])) {

    // Si el cliente intenta devolver algo que no ha reservado
    if (!$reserva) {
        $_SESSION['flash'][] = [
            'type' => 'error',
            'text' => '❌ No puedes devolver un artículo que no has reservado.'
        ];
    } else {

        // Se elimina la reserva únicamente del cliente actual
        $stmt = $conexion->prepare(
            "DELETE FROM Reservas 
             WHERE $campo = ? AND IdCliente = ?"
        );
        $stmt->bind_param("ii", $idItem, $idCliente);
        $stmt->execute();
        $stmt->close();

        // Se cambia el estado del libro o película a "Disponible"
        $db->cambiarEstado($tabla, $idItem, "Disponible");

        // Mensaje informativo para el usuario
        $_SESSION['flash'][] = [
            'type' => 'info',
            'text' => '🔄 Artículo devuelto correctamente.'
        ];
    }

/* ==================================================
   RESERVAR ARTÍCULO
   ================================================== */
} else {

    // Si el cliente ya tiene reservado este artículo
    if ($reserva) {
        $_SESSION['flash'][] = [
            'type' => 'error',
            'text' => '❌ Ya tienes este artículo reservado.'
        ];
    } else {

        // Inserción de la reserva según sea libro o película
        if ($tabla === "Libros") {
            // Reserva de un libro
            $stmt = $conexion->prepare(
                "INSERT INTO Reservas (IdCliente, IdLibro, IdPeliculas, Fecha_Reserva)
                 VALUES (?, ?, NULL, NOW())"
            );
        } else {
            // Reserva de una película
            $stmt = $conexion->prepare(
                "INSERT INTO Reservas (IdCliente, IdLibro, IdPeliculas, Fecha_Reserva)
                 VALUES (?, NULL, ?, NOW())"
            );
        }

        // Se asocian los valores a la consulta preparada
        $stmt->bind_param("ii", $idCliente, $idItem);
        $stmt->execute();
        $stmt->close();

        // Se cambia el estado del artículo a "Reservado"
        $db->cambiarEstado($tabla, $idItem, "Reservado");

        // Mensaje de confirmación
        $_SESSION['flash'][] = [
            'type' => 'success',
            'text' => '✅ Reserva realizada correctamente.'
        ];
    }
}

// Se vuelve al catálogo manteniendo los filtros anteriores
$volver = $_SESSION['volver_catalogo'] ?? "catalogo.php";
header("Location: $volver");
exit();
?>
