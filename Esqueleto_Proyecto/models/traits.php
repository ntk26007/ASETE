<?php

    trait Formateador {
        public function toHTML() {
            return "<strong>{$this->titulo}</strong> ({$this->año})<br>";
        }

        public function toJSON() {
            return json_encode($this);
        }
    }


?>