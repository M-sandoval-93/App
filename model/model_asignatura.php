<?php

    include_once "../model/model_conexion.php";

    class Asignatura extends Conexion {
        public function __construct() {
            parent:: __construct();
        }

        public function getAsignatura() {
            $query = "SELECT * FROM asignatura";
            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $asignaturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($asignaturas as $asignatura) {
                $this->json[] = $asignatura;
            }

            $this->closeConnection();
            return json_encode($this->json);


        }



    }




?>