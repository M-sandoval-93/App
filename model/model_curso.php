<?php

    // SE INCLUYE EL ARCHIVO DE CONEXION BBDD
    include_once "../model/model_conexion.php";
    include_once "../config/listas.php";


    class Curso extends Conexion {
        protected $query_crear = "INSERT INTO cursos(curso) VALUES (?);";
        // consulta que busca el curso correspondiente al año lectivo
        protected $query_consultar = "SELECT curso FROM cursos WHERE curso LIKE ? AND anio_curso = EXTRACT(YEAR FROM NOW()) LIMIT 1;";
        // recuperar las letras del grado
        protected $query_letras = "SELECT id_curso, substr(curso, 2,2) as curso FROM curso WHERE curso LIKE ? AND anio_curso = EXTRACT(YEAR FROM NOW()) ORDER BY curso ASC;";
        // private $json = array();

        public function __construct() {
            parent:: __construct();
        }

        public function generarCurso($grado, $letraHasta) {
            foreach (LETRAS as $letra) {
                if ($letra <= $letraHasta) {
                    $curso = $grado.$letra;
                    $sentencia = $this->conexion_db->prepare($this->query_crear);
                    $resultado = $sentencia->execute([$curso]);
                }
            }

            // DEVUELVE EL RESULTADO SI LA CREACIÓN A SIDO EXITOSA
            if ($resultado === true) {
                return json_encode(true);
            } else {
                return json_encode(false);
            }

        }

        public function consultarCurso($grado) {
            $sentencia = $this->conexion_db->prepare($this->query_consultar);
            $sentencia->execute([$grado.'%']);

            if ($sentencia->rowCount() >= 1) {
                return json_encode(false);
            } else {
                return json_encode(true);
            }

        }

        public function cargarLetrasGrado($grado) {
            $sentencia = $this->conexion_db->prepare($this->query_letras);
            $sentencia->execute([$grado.'%']);
            $cursos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            $option[] = "<option disable selected>Letra</option>";

            foreach ($cursos as $curso) {
                $option[] = "<option value='".$curso['id_curso']."' >".$curso['curso']."</option>";
            }

            return json_encode($option);
            $this->conexion_db = null;
        }
    } 


/*     NOTA: PARA REINICIAR EL CONTADOR DEL CAMPO AUTOINCREMENTO
    alter sequence cursos_id_curso_seq restart with 1; */



?>