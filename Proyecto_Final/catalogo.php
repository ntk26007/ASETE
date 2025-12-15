<?php 
session_start();
require_once 'Peliculas.php';
require_once 'DB.php';
include 'idioma.php';
include 'Utils.php';
include 'conexion.php';

// Proteger página
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Contador de visitas
if (!isset($_SESSION['visitas'])) $_SESSION['visitas'] = 0;
$visitas = &$_SESSION['visitas'];
Utils::incrementarVisitas($visitas);

// Inicializar DB
$db = new DB($conexion);

// --- Tipos seleccionados en index.php ---
$tipos = $_GET['tipo'] ?? [];

// --- Filtros ---
$filtrosPeliculas = [
    'titulo'   => $_GET['titulo_pelicula'] ?? '',
    'genero'   => $_GET['genero_pelicula'] ?? '',
    'año'      => $_GET['año'] ?? '',
    'director' => $_GET['director'] ?? '',
    'actor'    => $_GET['actor'] ?? ''
];

$filtrosLibros = [
    'titulo'   => $_GET['titulo_libro'] ?? '',
    'genero'   => $_GET['genero_libro'] ?? '',
    'autor'    => $_GET['autor'] ?? '',
    'editorial'=> $_GET['editorial'] ?? ''
];

// Datos según filtros
$peliculas = in_array("peliculas", $tipos) ? $db->getPeliculas($filtrosPeliculas) : [];
$libros    = in_array("libros", $tipos)    ? $db->getLibros($filtrosLibros)     : [];

// Mensajes flash
$mensaje = '';
if (isset($_SESSION['flash'])) {
    foreach ($_SESSION['flash'] as $flash) {
        $mensaje .= "<p class='{$flash['type']}'>{$flash['text']}</p>";
    }
    unset($_SESSION['flash']);
}

// Construir URL para volver con filtros
$parametros = http_build_query($_GET);
$volver_url = "catalogo.php?" . $parametros;
$_SESSION['volver_catalogo'] = $volver_url;

// Obtener clientes
$clientes = $conexion->query("SELECT id, nombre FROM Clientes ORDER BY nombre");

// Cliente actual (si lo hay en sesión)
$idClienteActual = $_SESSION['idCliente'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Catálogo</title>
<link rel="stylesheet" href="style.css">
<style>
/* Grid para dos columnas */
.catalogo-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 20px;
}

.catalogo-col {
    background-color: #9b6ae2;
    padding: 15px;
    border-radius: 10px;
    box-shadow: 0px 2px 5px rgba(0,0,0,0.1);
}
</style>
</head>
<body>

<h1><?= $lang_data['titulo_catalogo'] ?></h1>

<div class="idiomas">
    🌐 
    <a href="idioma.php?lang=es">Español</a> | 
    <a href="idioma.php?lang=en">English</a>
</div>


<div class="container">
    <button class="boton-flecha" onclick="window.location.href='index.php'"></button>
    <button class="nueva-box" onclick="window.location.href='añadir.php'"><?= $lang_data["añadir"] ?></button>
    <button class="nueva-box" onclick="window.location.href='logout.php'"><?= $lang_data["cerrar_sesion"] ?></button>
    <button class="nueva-box" onclick="window.location.href='mis_reservas.php'"><?= $lang_data["mis_reservas"] ?></button>
</div>

<div class="resultados">
    <?= $mensaje ?>
    <div class="catalogo-container">

        <!-- PELÍCULAS -->
    <?php if (in_array("peliculas", $tipos)): ?>
    <div class="catalogo-col">
        <h2>🎬 <?= $lang_data['peliculas'] ?></h2>
        <?php if (count($peliculas) > 0): ?>
            <?php foreach ($peliculas as $p): ?>
                <?= $p->aHTML($lang_data) ?>

                <?php
                // Verificar si la película está reservada
                $stmt = $conexion->prepare("SELECT * FROM Reservas WHERE IdPeliculas = ?");
                $stmt->bind_param("i", $p->id);
                $stmt->execute();
                $res = $stmt->get_result();
                $estaReservado = $res->num_rows > 0;
                $stmt->close();
                ?>

                <?php if ($estaReservado): ?>
                    <form action="reservar.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id_item" value="<?= $p->id ?>">
                        <input type="hidden" name="tabla" value="Peliculas">
                        <button type="submit" name="devolver" class="boton-devolver">
                            <?= $lang_data['devolver'] ?>
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btnAbrirModal boton-reservar"
                            data-id="<?= $p->id ?>"
                            data-tabla="Peliculas">
                        <?= $lang_data['reservar'] ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>❌ <?= $lang_data['no_hay_peliculas'] ?? 'No hay películas que coincidan con los filtros.' ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

        <!-- LIBROS -->
    <?php if (in_array("libros", $tipos)): ?>
    <div class="catalogo-col">
        <h2>📚 <?= $lang_data['libros'] ?></h2>
        <?php if (count($libros) > 0): ?>
            <?php foreach ($libros as $l): ?>
                <?= $l->aHTML($lang_data) ?>

                <?php
                $stmt = $conexion->prepare("SELECT * FROM Reservas WHERE IdLibro = ?");
                $stmt->bind_param("i", $l->id);
                $stmt->execute();
                $res = $stmt->get_result();
                $estaReservado = $res->num_rows > 0;
                $stmt->close();
                ?>

                <?php if ($estaReservado): ?>
                    <form action="reservar.php" method="POST" style="display:inline;">
                        <input type="hidden" name="id_item" value="<?= $l->id ?>">
                        <input type="hidden" name="tabla" value="Libros">
                        <button type="submit" name="devolver" class="boton-devolver">
                            <?= $lang_data['devolver'] ?>
                        </button>
                    </form>
                <?php else: ?>
                    <button class="btnAbrirModal boton-reservar"
                            data-id="<?= $l->id ?>"
                            data-tabla="Libros">
                        <?= $lang_data['reservar'] ?>
                    </button>
                <?php endif; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <p>❌ <?= $lang_data['no_hay_libros'] ?? 'No hay libros que coincidan con los filtros.' ?></p>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    </div>
    <br>
    <p><?= $lang_data['visitas'] ?> <strong><?= $_SESSION['visitas'] ?></strong> veces.</p>
</div>

<!-- Modal seleccionar cliente solo para reservas -->
<div id="modalCliente" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h3>Selecciona un cliente:</h3>
        <form id="formCliente" method="POST" action="reservar.php">
            <input type="hidden" name="id_item" id="modal_id_item">
            <input type="hidden" name="tabla" id="modal_tabla">
            
            <select name="idCliente" required>
                <option value="">-- Selecciona un cliente --</option>
                <?php
                $clientes->data_seek(0);
                while($c = $clientes->fetch_assoc()) {
                    echo "<option value='{$c['id']}'>{$c['nombre']}</option>";
                }
                ?>
            </select>
            <br><br>
            <button type="submit" name="reservar">Aceptar</button>
        </form>
    </div>
</div>

<script>
const modal = document.getElementById("modalCliente");
const modalId = document.getElementById("modal_id_item");
const modalTabla = document.getElementById("modal_tabla");
const spanClose = document.querySelector(".modal .close");

// Abrir modal solo para reservas
document.querySelectorAll(".btnAbrirModal").forEach(btn => {
    btn.addEventListener("click", () => {
        modal.style.display = "flex";
        modalId.value = btn.getAttribute("data-id");
        modalTabla.value = btn.getAttribute("data-tabla");
    });
});

// Cerrar modal
spanClose.onclick = () => modal.style.display = "none";
window.onclick = (e) => { if(e.target == modal) modal.style.display = "none"; };
</script>

</body>
</html>

