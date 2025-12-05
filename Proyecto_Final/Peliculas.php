<?php
require_once "Formatear.php";

class Pelicula {
    use Formatear;

    public string $titulo;
    public string $año;
    public string $director;
    public string $actores;
    public string $genero;
    public string $estado = "Disponible";  // ← Añadido
    public int $id;

    public function __construct($titulo, $año, $director, $actores, $genero) {
        $this->titulo = $titulo;
        $this->año = $año;
        $this->director = $director;
        $this->actores = $actores;
        $this->genero = $genero;
    }
}

class Libro {
    use Formatear;

    public string $titulo;
    public int $autor_id;
    public string $genero;
    public string $editorial;
    public int $paginas;
    public string $año;
    public int $precio;
    public string $estado = "Disponible"; // ← Añadido
    public int $id;

    public function __construct($titulo, $autor_id, $genero, $editorial, $paginas, $año, $precio) {
        $this->titulo = $titulo;
        $this->autor_id = $autor_id;
        $this->genero = $genero;
        $this->editorial = $editorial;
        $this->paginas = $paginas;
        $this->año = $año;
        $this->precio = $precio;
    }
}
?>