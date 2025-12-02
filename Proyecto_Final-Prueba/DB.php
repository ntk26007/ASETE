<?php
require_once 'Peliculas.php'; // Incluye Pelicula y Libro

class DB {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // --- Obtener películas ---
    public function getPeliculas($filtros = []) {
        $sql = "SELECT * FROM Peliculas WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filtros['titulo'])) {
            $sql .= " AND Titulo LIKE ?";
            $params[] = "%".$filtros['titulo']."%";
            $types .= 's';
        }
        if (!empty($filtros['genero'])) {
            $sql .= " AND Genero LIKE ?";
            $params[] = "%".$filtros['genero']."%";
            $types .= 's';
        }
        if (!empty($filtros['director'])) {
            $sql .= " AND Director LIKE ?";
            $params[] = "%".$filtros['director']."%";
            $types .= 's';
        }
        if (!empty($filtros['actor'])) {
            $sql .= " AND Actores LIKE ?";
            $params[] = "%".$filtros['actor']."%";
            $types .= 's';
        }
        if (!empty($filtros['año'])) {
            $sql .= " AND YEAR(Año_estreno) = ?";
            $params[] = $filtros['año'];
            $types .= 'i';
        }

        $stmt = $this->conexion->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $peliculas = [];
        while ($fila = $resultado->fetch_assoc()) {
            $p = new Pelicula(
                $fila['Titulo'],
                $fila['Año_estreno'],
                $fila['Director'],
                $fila['Actores'],
                $fila['Genero'],
                $fila['Estado'] ?? 'Disponible'
            );
            $p->id = $fila['ID'];
            $peliculas[] = $p;
        }
        return $peliculas;
    }

    // --- Obtener libros ---
    public function getLibros($filtros = []) {
        $sql = "SELECT * FROM Libros WHERE 1=1";
        $params = [];
        $types = '';

        if (!empty($filtros['titulo'])) {
            $sql .= " AND Titulo LIKE ?";
            $params[] = "%".$filtros['titulo']."%";
            $types .= 's';
        }
        if (!empty($filtros['genero'])) {
            $sql .= " AND Genero LIKE ?";
            $params[] = "%".$filtros['genero']."%";
            $types .= 's';
        }
        if (!empty($filtros['editorial'])) {
            $sql .= " AND Editorial LIKE ?";
            $params[] = "%".$filtros['editorial']."%";
            $types .= 's';
        }
        if (!empty($filtros['año'])) {
            $sql .= " AND YEAR(Año) = ?";
            $params[] = $filtros['año'];
            $types .= 'i';
        }

        $stmt = $this->conexion->prepare($sql);
        if (!empty($params)) $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $resultado = $stmt->get_result();

        $libros = [];
        while ($fila = $resultado->fetch_assoc()) {
            $l = new Libro(
                $fila['Titulo'],
                $fila['Autor_id'],
                $fila['Genero'],
                $fila['Editorial'],
                $fila['Paginas'],
                $fila['Año'],
                $fila['Precio'],
                $fila['Estado'] ?? 'Disponible'
            );
            $l->id = $fila['ID'];
            $libros[] = $l;
        }
        return $libros;
    }

    // --- Cambiar estado de película o libro ---
    public function cambiarEstado($tabla, $id, $nuevoEstado) {
        $stmt = $this->conexion->prepare("UPDATE $tabla SET Estado=? WHERE ID=?");
        $stmt->bind_param('si', $nuevoEstado, $id);
        return $stmt->execute();
    }
}
?>
