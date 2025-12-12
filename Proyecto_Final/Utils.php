<?php
/*
Clase Utils: funciones auxiliares del proyecto.
Contiene filtrado, incrementar visitas y añadir película a la lista.
*/
class Utils {
    // Filtra películas según formulario
    public static function filtrarPeliculas($peliculas, $filtros){
        $resultados = [];
        foreach ($peliculas as $peli) {
            // ignorar elementos que no sean instancias de Pelicula
            if (!($peli instanceof Pelicula)) 
                continue;
            $coincide = true;

            // FILTRO: géneros (si hay array de géneros, comprobamos inclusión)
            if (!empty($filtros['generos']) && is_array($filtros['generos'])) {
            // si la película no está en ninguno de los géneros seleccionados -> excluir
            if (!in_array($peli->genero, $filtros['generos'], true)) {
                $coincide = false;
            }
        }

        //filtro: título
        if ($coincide && !empty($filtros['año'])) {
            if ((string)$peli->año !== (string)$filtros['año']) $coincide = false;
        }

        //filtro: director
        if ($coincide && !empty($filtros['director'])) {
            if (stripos($peli->director, $filtros['director']) === false) $coincide = false;
        }

        //filtro: actor
        if ($coincide && !empty($filtros['actor'])) {
            if (stripos($peli->actores, $filtros['actor']) === false) $coincide = false;
        }

        if ($coincide) $resultados[] = $peli;
    }
    return $resultados;
}

// Filtra libros según formulario
public static function filtrarLibros($libros, $filtros) {
    $resultados = [];

    foreach ($libros as $libro) {

        // ignorar elementos que no sean instancia de Libro
        if (!($libro instanceof Libro)) 
            continue;

        $coincide = true;

        // Filtro: título
        if (!empty($filtros['titulo'])) {
            if (stripos($libro->titulo, $filtros['titulo']) === false) {
                $coincide = false;
            }
        }

        // Filtro: género
        if ($coincide && !empty($filtros['genero'])) {
            if (stripos($libro->genero, $filtros['genero']) === false) {
                $coincide = false;
            }
        }

        // Filtro: autor
        if ($coincide && !empty($filtros['autor'])) {
            if (stripos($libro->autor, $filtros['autor']) === false) {
                $coincide = false;
            }
        }

        // Filtro: editorial
        if ($coincide && !empty($filtros['editorial'])) {
            if (stripos($libro->editorial, $filtros['editorial']) === false) {
                $coincide = false;
            }
        }

        if ($coincide) $resultados[] = $libro;
    }

    return $resultados;
}


    //Incrementa las visitas al catálogo
    public static function incrementarVisitas(&$contador){
        $contador++;
    }
    //Añadir peliula desde el catálogo
    public static function añadirPelicula(&$peliculas, $nuevaPeli){
        $peliculas[] = $nuevaPeli;
    }
}
?>