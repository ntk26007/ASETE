<?php
// Inicia la sesión para acceder a los datos del usuario autenticado
session_start();

// Si el cliente no está autenticado, se redirige al login
if (!isset($_SESSION['idCliente'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
require_once "conexion.php";

// Clase DB (no se usa directamente aquí, pero se incluye por coherencia del proyecto)
require_once "DB.php";

// Archivo de idioma para textos traducibles
include "idioma.php";

// Se obtiene el ID del cliente desde la sesión
$idCliente = $_SESSION['idCliente'];

/* ==================================================
   CONSULTA DE RESERVAS DEL CLIENTE
   ================================================== */

// Consulta que obtiene todas las reservas del cliente, tanto libros como películas
// Se usan LEFT JOIN para poder mostrar el título según el tipo de artículo
$sql = "
SELECT 
    r.Fecha_Reserva,           -- Fecha en la que se realizó la reserva
    r.IdLibro,                 -- ID del libro (si es una reserva de libro)
    r.IdPeliculas,             -- ID de la película (si es una reserva de película)
    CASE 
        WHEN r.IdLibro IS NOT NULL THEN 'Libro'
        ELSE 'Película'
    END AS Tipo,               -- Determina si la reserva es de libro o película
    CASE 
        WHEN r.IdLibro IS NOT NULL THEN l.Titulo
        ELSE p.Titulo
    END AS Titulo               -- Obtiene el título correspondiente
FROM Reservas r
LEFT JOIN Libros l ON r.IdLibro = l.ID
LEFT JOIN Peliculas p ON r.IdPeliculas = p.ID
WHERE r.IdCliente = ?           -- Solo reservas del cliente logueado
ORDER BY r.Fecha_Reserva DESC   -- Ordenadas por fecha (más reciente primero)
";

// Se prepara la consulta
$stmt = $conexion->prepare($sql);

// Se vincula el ID del cliente al parámetro de la consulta
$stmt->bind_param("i", $idCliente);

// Se ejecuta la consulta
$stmt->execute();

// Se obtiene el conjunto de resultados
$res = $stmt->get_result();

// Se cierra la sentencia preparada
$stmt->close();

// URL para volver al catálogo manteniendo los filtros
$volver_url = $_SESSION['volver_catalogo'] ?? 'catalogo.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Título de la página con el nombre del usuario -->
<h1>📚 <?= $_SESSION['usuario'] ?> - <?= $lang_data["mis_reservas"] ?></h1>

<!-- Selector de idioma -->
<div class="idiomas">
    🌐 
    <a href="idioma.php?lang=es">Español</a> | 
    <a href="idioma.php?lang=en">English</a>
</div>

<!-- Botones de navegación -->
<div class="container">
    <div class="nueva-cerrar-box">
        <!-- Botón para volver al catálogo -->
        <button class="nueva-box" onclick="window.location.href='<?= $volver_url ?>'">
            <?= $lang_data["volver"] ?>
        </button>

        <!-- Botón para cerrar sesión -->
        <button class="nueva-box" onclick="window.location.href='logout.php'">
            <?= $lang_data["cerrar_sesion"] ?>
        </button>
    </div>

    <!-- Resultados de las reservas -->
    <div class="resultados">
        <?php if ($res->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th><?= $lang_data["titulo"] ?></th>
                        <th><?= $lang_data["tipo"] ?></th>
                        <th><?= $lang_data["fecha_reserva"] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $res->fetch_assoc()): ?>
                        <tr>
                            <!-- Muestra el ID del libro o de la película -->
                            <td><?= $row['IdLibro'] ?? $row['IdPeliculas'] ?></td>

                            <!-- Muestra el título del artículo reservado -->
                            <td><?= htmlspecialchars($row['Titulo']) ?></td>

                            <!-- Muestra el tipo de reserva -->
                            <td><?= $row['Tipo'] ?></td>

                            <!-- Muestra la fecha de la reserva -->
                            <td><?= $row['Fecha_Reserva'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <!-- Mensaje cuando no hay reservas activas -->
            <p><?= $lang_data['reservas_activas'] ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
