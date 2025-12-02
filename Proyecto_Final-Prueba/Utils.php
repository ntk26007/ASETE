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
            if (!empty($filtros['generos']) && !!in_array($peli->getGenero(), $filtros['generos'])) {
                $coincide = false;
            }   
            if ($coincide && !empty($filtros['año']) && $peli['año'] != $filtros['año']) {
                $coincide = false;
            }
            if ($coincide && !empty($filtros['director']) && stripos($peli['director'], $filtros['director']) === false) {
                $coincide = false;
            }
            if ($coincide && !empty($filtros['actor']) && stripos($peli['actores'], $filtros['actor']) === false) {
                $coincide = false;
            }
            if ($coincide) {
                $resultados[] = $peli;
            }
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