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
        //bind_param = Vincula variables a una sentencia preparada como parámetros
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $peliculas = [];

        //fetch_assoc = Obtiene una fila de resultados como un array asociativo
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
        $sql = "SELECT l.*, a.NOMBRE AS AutorNombre 
        FROM Libros l 
        LEFT JOIN Autores a ON a.ID = l.Autor_id 
        WHERE 1=1";
        $params = [];
        $types = "";

        if (!empty($filtros['titulo'])) { $sql .= " AND l.Titulo LIKE ?"; $params[] = "%".$filtros['titulo']."%"; $types .= "s"; }
        if (!empty($filtros['genero'])) { $sql .= " AND l.Genero LIKE ?"; $params[] = "%".$filtros['genero']."%"; $types .= "s"; }
        if (!empty($filtros['editorial'])) { $sql .= " AND l.Editorial LIKE ?"; $params[] = "%".$filtros['editorial']."%"; $types .= "s"; }
        if (!empty($filtros['año'])) { $sql .= " AND YEAR(l.Año) = ?"; $params[] = $filtros['año']; $types .= "i"; }
        if (!empty($filtros['autor'])) { $sql .= " AND a.NOMBRE LIKE ?"; $params[] = "%".$filtros['autor']."%"; $types .= "s"; }

        $stmt = $this->conexion->prepare($sql);
        if (!$stmt) return [];
        if (!empty($params)) $stmt->bind_param($types, ...$params);

        //execute = Ejecuta una sentencia preparada y get_result = Obtiene un conjunto de resultados de una sentencia preparada
        $stmt->execute();
        $res = $stmt->get_result();
        $libros = [];

        while ($fila = $res->fetch_assoc()) {
            $l = new Libro(
                $fila['Titulo'],
                $fila['Autor_id'],
                $fila['AutorNombre'],
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
        OBTENER PELÍCULA O LIBRO POR ID
        ------------------------------------------------------------------------------------ */
    public function getItemPorId($tabla, $id) {
    if ($tabla === "Peliculas") {
        $query = "SELECT * FROM Peliculas WHERE ID = ?";
    } else {
        $query = "SELECT l.*, a.NOMBRE AS AutorNombre
                  FROM Libros l
                  LEFT JOIN Autores a ON a.ID = l.Autor_id
                  WHERE l.ID = ?";
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
            $res['AutorNombre'], // ahora sí existe
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
    public function insertarPelicula($titulo, $año, $director, $actores, $genero, $tipo_adaptacion = 'pelicula') {

        // Si el usuario pone solo "2020" → lo convertimos a "2020-01-01"
        if (preg_match('/^\d{4}$/', $año)) {
            $año = $año . "-01-01";
        }

        // Validar formato completo YYYY-MM-DD
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $año)) {
            throw new Exception("La fecha debe tener formato YYYY o YYYY-MM-DD.");
        }

        $sql = "INSERT INTO Peliculas (Titulo, Año_estreno, Director, Actores, Genero, Tipo_adaptacion, Estado)
                VALUES (?, ?, ?, ?, ?, ?, 'Disponible')";

        $stmt = $this->conexion->prepare($sql);

        if (!$stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $this->conexion->error);
        }

        // Todos son strings → s s s s s s
        //bind_param = Vincula variables a una sentencia preparada como parámetros y se pone ssssss porque son 6 strings
        $stmt->bind_param("ssssss",
            $titulo,
            $año,
            $director,
            $actores,
            $genero,
            $tipo_adaptacion
        );

        return $stmt->execute();
    }


    /* ------------------------------------------------------------------------------------
    INSERTAR NUEVO LIBRO
    ------------------------------------------------------------------------------------ */
   public function insertarLibro($titulo, $genero, $autor_id, $editorial, $paginas = 0, $año = null, $precio = 0) {

    if ($año === null || $año === "") {

        $sql = "INSERT INTO Libros (Titulo, Autor_id, Genero, Editorial, Paginas, Año, Precio, Estado)
                VALUES (?, ?, ?, ?, ?, NULL, ?, 'Disponible')";
        
        $stmt = $this->conexion->prepare($sql);
        //se pone sissii porque son 6 parametros: string, int, string, string, int, int
        $stmt->bind_param("sissii",
            $titulo,
            $autor_id,
            $genero,
            $editorial,
            $paginas,
            $precio
        );

    } else {

        // Convertir año de "YYYY" → "YYYY-01-01"
        if (preg_match('/^\d{4}$/', $año)) {
            $año = $año . "-01-01";
        }

        $sql = "INSERT INTO Libros (Titulo, Autor_id, Genero, Editorial, Paginas, Año, Precio, Estado)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'Disponible')";
        
        $stmt = $this->conexion->prepare($sql);

        // 🔥 Tipos correctos: s i s s i s i
        $stmt->bind_param("sissisi",
            $titulo,
            $autor_id,
            $genero,
            $editorial,
            $paginas,
            $año,
            $precio
        );
    }

    return $stmt->execute();
}
}
?>
