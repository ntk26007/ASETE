<?php
require_once "Peliculas.php";

class DB {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->asegurarColumnas();
    }

    /* ------------------------------------------------------------------------------------
        AÑADIR AUTOMÁTICAMENTE COLUMNA "Estado" SI NO EXISTE EN PELICULAS Y LIBROS
    ------------------------------------------------------------------------------------ */
    private function asegurarColumnas() {
        // ----- Tabla Peliculas -----
        $sql = "SHOW COLUMNS FROM Peliculas LIKE 'Estado'";
        $res = $this->conexion->query($sql);

        if ($res->num_rows == 0) {
            $this->conexion->query(
                "ALTER TABLE Peliculas ADD Estado VARCHAR(20) NOT NULL DEFAULT 'Disponible'"
            );
        }

        // ----- Tabla Libros -----
        $sql2 = "SHOW COLUMNS FROM Libros LIKE 'Estado'";
        $res2 = $this->conexion->query($sql2);

        if ($res2->num_rows == 0) {
            $this->conexion->query(
                "ALTER TABLE Libros ADD Estado VARCHAR(20) NOT NULL DEFAULT 'Disponible'"
            );
        }
    }

    /* ------------------------------------------------------------------------------------
        OBTENER PELÍCULAS
    ------------------------------------------------------------------------------------ */
    public function getPeliculas($filtros = []) {
        $sql = "SELECT * FROM Peliculas WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filtros['titulo'])) { $sql .= " AND Titulo LIKE ?"; $params[] = "%".$filtros['titulo']."%"; $types .= "s"; }
        if (!empty($filtros['genero'])) { $sql .= " AND Genero LIKE ?"; $params[] = "%".$filtros['genero']."%"; $types .= "s"; }
        if (!empty($filtros['director'])) { $sql .= " AND Director LIKE ?"; $params[] = "%".$filtros['director']."%"; $types .= "s"; }
        if (!empty($filtros['actor'])) { $sql .= " AND Actores LIKE ?"; $params[] = "%".$filtros['actor']."%"; $types .= "s"; }
        if (!empty($filtros['año'])) { $sql .= " AND YEAR(Año_estreno) = ?"; $params[] = $filtros['año']; $types .= "i"; }

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) {
            // Manejo de error (log y devolver array vacío)
            error_log("DB prepare error: " . $this->conexion->error);
        return [];
        }
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $peliculas = [];

        while ($fila = $result->fetch_assoc()) {
            $p = new Pelicula(
                $fila["Titulo"],
                $fila["Año_estreno"],
                $fila["Director"],
                $fila["Actores"],
                $fila["Genero"]
            );
            $p->id = $fila["ID"];
            $p->estado = $fila["Estado"];      // ← IMPORTANTE

            $peliculas[] = $p;
        }
        return $peliculas;
    }

    /* ------------------------------------------------------------------------------------
        OBTENER LIBROS
    ------------------------------------------------------------------------------------ */
    public function getLibros($filtros = []) {
        $sql = "SELECT * FROM Libros WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filtros['titulo'])) { $sql .= " AND Titulo LIKE ?"; $params[] = "%".$filtros['titulo']."%"; $types .= "s"; }
        if (!empty($filtros['genero'])) { $sql .= " AND Genero LIKE ?"; $params[] = "%".$filtros['genero']."%"; $types .= "s"; }
        if (!empty($filtros['editorial'])) { $sql .= " AND Editorial LIKE ?"; $params[] = "%".$filtros['editorial']."%"; $types .= "s"; }
        if (!empty($filtros['año'])) { $sql .= " AND YEAR(Año) = ?"; $params[] = $filtros['año']; $types .= "i"; }

        $stmt = $this->conexion->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $res = $stmt->get_result();
        $libros = [];

        while ($fila = $res->fetch_assoc()) {
            $l = new Libro(
                $fila['Titulo'],
                $fila['Autor_id'],
                $fila['Genero'],
                $fila['Editorial'],
                $fila['Paginas'],
                $fila['Año'],
                $fila['Precio']
            );
            $l->id = $fila["ID"];
            $l->estado = $fila["Estado"];    // ← IMPORTANTE

            $libros[] = $l;
        }
        return $libros;
    }

    public function getItemPorId($tabla, $id) {
    if ($tabla === "Peliculas") {
        $query = "SELECT * FROM Peliculas WHERE ID = ?";
    } else {
        $query = "SELECT * FROM Libros WHERE ID = ?";
    }

    $stmt = $this->conexion->prepare($query);
    if (!$stmt) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();

    if (!$res) return null;

    if ($tabla === "Peliculas") {
        $item = new Pelicula(
            $res['Titulo'],
            $res['Año_estreno'],
            $res['Director'],
            $res['Actores'],
            $res['Genero']
        );
    } else {
        $item = new Libro(
            $res['Titulo'],
            $res['Autor_id'],
            $res['Genero'],
            $res['Editorial'],
            $res['Paginas'],
            $res['Año'],
            $res['Precio']
        );
    }

    $item->id = $res['ID'];
    $item->estado = $res['Estado'];

    return $item;
}

    /* ------------------------------------------------------------------------------------
        CAMBIAR ESTADO (Reserva)
    ------------------------------------------------------------------------------------ */
    public function cambiarEstado($tabla, $id, $nuevoEstado) {
        if ($tabla === "Peliculas") {
             $sql = "UPDATE Peliculas SET Estado = ? WHERE ID = ?";
        } else {
             $sql = "UPDATE Libros SET Estado = ? WHERE ID = ?";
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $nuevoEstado, $id);
        $stmt->execute();
    }

    /* ------------------------------------------------------------------------------------
    INSERTAR NUEVA PELÍCULA O LIBRO
    ------------------------------------------------------------------------------------ */
    public function insertarPelicula($titulo, $año, $director, $actores, $genero) {
        $sql = "INSERT INTO peliculas (Titulo, Año, Director, Actores, Genero, Estado)
                VALUES (?, ?, ?, ?, ?, 'Disponible')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sisss", $titulo, $año, $director, $actores, $genero);
        return $stmt->execute();
    }

    public function insertarLibro($titulo, $genero, $autor_id, $editorial, $paginas = 0, $año = null, $precio = 0) {

    if ($año === null || $año === "") {
        $sql = "INSERT INTO Libros (Titulo, Autor_id, Genero, Editorial, Paginas, Año, Precio, Estado)
                VALUES (?, ?, ?, ?, ?, NULL, ?, 'Disponible')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sissii", $titulo, $autor_id, $genero, $editorial, $paginas, $precio);
    } else {

        // Convertir año simple → fecha válida
        if (preg_match('/^\d{4}$/', $año)) {
            $año = $año . "-01-01";
        }

        $sql = "INSERT INTO Libros (Titulo, Autor_id, Genero, Editorial, Paginas, Año, Precio, Estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Disponible')";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("sissisi", $titulo, $autor_id, $genero, $editorial, $paginas, $año, $precio);
    }

    return $stmt->execute();
}

}
?>
