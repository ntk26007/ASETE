<?php
// Trait Formatear: proporciona métodos para formatear la información de un objeto
trait Formatear {

    public function aHTML(array $lang_data = [], array $valores_traducidos = []) {

        // Etiquetas
        $titulo_label   = $lang_data['titulo']   ?? 'Título';
        $año_label      = $lang_data['año']      ?? 'Año';
        $director_label = $lang_data['director'] ?? 'Director';
        $actores_label  = $lang_data['actor']    ?? 'Actores';
        $genero_label   = $lang_data['generos']  ?? 'Género';

        // Valores comunes
        //htmlspecialchars = Convierte caracteres especiales HTML en texto seguro
        //Evita que el navegador interprete código malicioso, protegiendo la aplicación frente a ataques XSS
        $titulo = htmlspecialchars($this->titulo ?? '', ENT_QUOTES, 'UTF-8');
        $año    = htmlspecialchars((string)($this->año ?? ''), ENT_QUOTES, 'UTF-8');
        $genero = htmlspecialchars($this->genero ?? '', ENT_QUOTES, 'UTF-8');

        if (!empty($valores_traducidos['genero'][$genero])) {
            $genero = $valores_traducidos['genero'][$genero];
        }

        // ================================
        //         SI ES PELÍCULA
        // ================================
        if ($this instanceof Pelicula) {

            $director = htmlspecialchars($this->director ?? '', ENT_QUOTES, 'UTF-8');
            $actores  = htmlspecialchars($this->actores ?? '', ENT_QUOTES, 'UTF-8');

            return "
            <table border='1'>
                <tr><td><strong>$titulo_label</strong></td><td>$titulo</td></tr>
                <tr><td><strong>$año_label</strong></td><td>$año</td></tr>
                <tr><td><strong>$director_label</strong></td><td>$director</td></tr>
                <tr><td><strong>$actores_label</strong></td><td>$actores</td></tr>
                <tr><td><strong>$genero_label</strong></td><td>$genero</td></tr>
            </table><br>";
        }

        // ================================
        //            SI ES LIBRO
        // ================================
        if ($this instanceof Libro) {
    $autor_label = $lang_data['autor'] ?? 'Autor';
    $precio_label = $lang_data['precio'] ?? 'Precio';

    $autor = htmlspecialchars($this->autor ?? '', ENT_QUOTES, 'UTF-8');
    $precio = htmlspecialchars((string)$this->precio ?? '', ENT_QUOTES, 'UTF-8');

    return "
    <table border='1'>
        <tr>
            <td><strong>{$titulo_label}</strong></td><td>{$titulo}</td>
        </tr>
        <tr>
            <td><strong>{$año_label}</strong></td><td>{$año}</td>
        </tr>
        <tr>
            <td><strong>{$autor_label}</strong></td><td>{$autor}</td>
        </tr>
        <tr>
            <td><strong>{$precio_label}</strong></td><td>{$precio} €</td>
        </tr>
        <tr>
            <td><strong>{$genero_label}</strong></td><td>{$genero}</td>
        </tr>
    </table><br>";
        }

        return '';
    }

    /*
    metodo aJSON: 
    Devuelve las propiedades públicas del objeto en JSON legible.
    */
    public function aJSON() {
         
        return json_encode(get_object_vars($this), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}
?>