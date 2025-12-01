<?php

    require_once "traits.php";

    class Pelicula {

        use Formateador;

        public $titulo;
        public $año;
        public $director;
        public $actores;
        public $genero;

        public function __construct($titulo, $año, $director, $actores, $genero){
            $this->titulo = $titulo;
            $this->año = $año;
            $this->director = $director;
            $this->actores = $actores;
            $this->genero = $genero;
        }

        public function mostrarPelicula(){
            return "<p>Título: $this->titulo</p>";
        }

    }


    class Serie extends Pelicula {

        public $n_temporadas;

        public function __construct($titulo, $año, $director, $actores, $genero, $n_temporadas){
            Pelicula::__construct($titulo, $año, $director, $actores, $genero);
            $this->n_temporadas = $n_temporadas;
        }

        public function mostrarPelicula(){
            return "<p>Título: $this->titulo es una serie.</p>";
        }

    }


    class Corto extends Pelicula {

        public $duracion;

        public function __construct($titulo, $año, $director, $actores, $genero, $duracion){
            Pelicula::__construct($titulo, $año, $director, $actores, $genero);
            $this->duracion = $duracion;
        }

        public function mostrarPelicula(){
            return "<p>Título: $this->titulo es un corto.</p>";
        }

    }

?>