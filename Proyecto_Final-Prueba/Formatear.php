<?php
// Trait Formatear: proporciona métodos para formatear la información de un objeto
trait Formatear {
    //Metodo aHTML: devuelve las propiedades públicas del objeto en formato HTML
    public function aHTML(array $lang_data = [], array $valores_traducidos = []) {
        
        //Si no se pasa nada, usamos valores por defecto en español
        $titulo_label = $lang_data['titulo'] ?? 'Título';
        $año_label = $lang_data['año'] ?? 'Año';
        $director_label = $lang_data['director'] ?? 'Director';
        $actores_label = $lang_data['actor'] ?? 'Actores';
        $genero_label = $lang_data['generos'] ?? 'Género';
        

        // Usamos htmlspecialchars sobre cada valor para prevenir XSS.
        $titulo = htmlspecialchars($this->titulo ?? '', ENT_QUOTES, 'UTF-8');
        $año = htmlspecialchars((string)($this->año ?? ''), ENT_QUOTES, 'UTF-8');
        $director = htmlspecialchars($this->director ?? '', ENT_QUOTES, 'UTF-8');
        $actores = htmlspecialchars($this->actores ?? '', ENT_QUOTES, 'UTF-8');
        $genero = htmlspecialchars($this->genero ?? '', ENT_QUOTES, 'UTF-8');

        // Si hay traducción de valores (por ejemplo géneros), aplicarla
        if (!empty($valores_traducidos['genero'][$genero])) {
            $genero = $valores_traducidos['genero'][$genero];
        }
        return "
        <table border='1'>
            <tr>
                <td><strong>{$titulo_label}</strong></td><td>{$titulo}</td>
            </tr>
            <tr>
                <td><strong>{$año_label}</strong></td><td>{$año}</td>
            </tr>
            <tr>
                <td><strong>{$director_label}</strong></td><td>{$director}</td>
            </tr>
            <tr>
                <td><strong>{$actores_label}</strong></td><td>{$actores}</td>
            </tr>
            <tr>
                <td><strong>{$genero_label}</strong></td><td>{$genero}</td>
            </tr>
        </table><br>";
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