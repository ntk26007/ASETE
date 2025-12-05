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

        if ($coincide && !empty($filtros['año'])) {
            if ((string)$peli->año !== (string)$filtros['año']) $coincide = false;
        }

        if ($coincide && !empty($filtros['director'])) {
            if (stripos($peli->director, $filtros['director']) === false) $coincide = false;
        }

        if ($coincide && !empty($filtros['actor'])) {
            if (stripos($peli->actores, $filtros['actor']) === false) $coincide = false;
        }

        if ($coincide) $resultados[] = $peli;
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