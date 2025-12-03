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

    /* ------------------------------------------------------------------------------------
        CAMBIAR ESTADO (Reserva)
    ------------------------------------------------------------------------------------ */
    public function cambiarEstado($tabla, $id, $nuevoEstado) {
        $sql = "UPDATE $tabla SET Estado=? WHERE ID=?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("si", $nuevoEstado, $id);
        return $stmt->execute();
    }
}
