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
$db = new DB($conexion);

// Obtener reservas del cliente
$sql = "SELECT r.IdLibro, r.Fecha_Reserva, l.Titulo 
        FROM Reservas r
        JOIN Libros l ON r.IdLibro = l.ID
        WHERE r.IdCliente = ?";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $idCliente);
$stmt->execute();
$res = $stmt->get_result();
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
        <button class="nueva-box" onclick="window.location.href='catalogo.php'"><?= $lang_data["volver"] ?></button>
        <button class="nueva-box" onclick="window.location.href='logout.php'"><?= $lang_data["cerrar_sesion"] ?></button>
    </div>

    <div class="resultados">
        <?php if ($res->num_rows > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th><?= $lang_data["id_libro"] ?></th>
                        <th><?= $lang_data["titulo"] ?></th>
                        <th><?= $lang_data["fecha_reserva"] ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $res->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['IdLibro'] ?></td>
                            <td><?= htmlspecialchars($row['Titulo']) ?></td>
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
