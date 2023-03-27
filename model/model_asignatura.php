<?php

    include_once "../model/model_conexion.php";

    class Asignatura extends Conexion {
        public function __construct() {
            parent:: __construct();
        }

        public function getAsignatura($grado) {
            $query = "SELECT id_asignatura, asignatura FROM asignatura
                WHERE grado LIKE ? || '%' OR grado LIKE '%' || ? || '%' OR grado LIKE '%' || ? OR grado = ?;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$grado, $grado, $grado, $grado]);
            $asignaturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($asignaturas as $asignatura) {
                $this->json[] = $asignatura;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

    }




?>