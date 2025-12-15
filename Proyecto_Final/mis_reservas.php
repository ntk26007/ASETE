<?php
session_start();

if (!isset($_SESSION['idCliente'])) {
    header("Location: login.php");
    exit();
}

require_once "conexion.php";
require_once "DB.php";
include "idioma.php";

$idCliente = $_SESSION['idCliente'];

$sql = "
SELECT 
    r.Fecha_Reserva,
    r.IdLibro,
    r.IdPeliculas,
    CASE 
        WHEN r.IdLibro IS NOT NULL THEN 'Libro'
        ELSE 'Película'
    END AS Tipo,
    CASE 
        WHEN r.IdLibro IS NOT NULL THEN l.Titulo
        ELSE p.Titulo
    END AS Titulo
FROM Reservas r
LEFT JOIN Libros l ON r.IdLibro = l.ID
LEFT JOIN Peliculas p ON r.IdPeliculas = p.ID
WHERE r.IdCliente = ?
ORDER BY r.Fecha_Reserva DESC
";

$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idCliente);
$stmt->execute();
$res = $stmt->get_result();
$stmt->close();

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

<h1>📚 <?= $_SESSION['usuario'] ?> - <?= $lang_data["mis_reservas"] ?></h1>

<div class="idiomas">
    🌐 
    <a href="idioma.php?lang=es">Español</a> | 
    <a href="idioma.php?lang=en">English</a>
</div>

<div class="container">
    <div class="nueva-cerrar-box">
        <button class="nueva-box" onclick="window.location.href='<?= $volver_url ?>'">
            <?= $lang_data["volver"] ?>
        </button>
        <button class="nueva-box" onclick="window.location.href='logout.php'">
            <?= $lang_data["cerrar_sesion"] ?>
        </button>
    </div>

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
                            <td><?= $row['IdLibro'] ?? $row['IdPeliculas'] ?></td>
                            <td><?= htmlspecialchars($row['Titulo']) ?></td>
                            <td><?= $row['Tipo'] ?></td>
                            <td><?= $row['Fecha_Reserva'] ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p><?= $lang_data['reservas_activas'] ?></p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
