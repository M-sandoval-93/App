<?php

    // SE INCLUYE EL ARCHIVO DE CONEXION BBDD
    include_once "../model/model_conexion.php";
    // include_once "../config/listas.php";


    class Curso extends Conexion {

        public function __construct() {
            parent:: __construct();
        }

        // Método para obtener los grados y la cantidad de estudiantes
        public function getGrado() {
            $query = "SELECT DISTINCT (substr(curso.curso, 1, 1)||'°') AS grado,
                CASE WHEN (substr(curso.curso, 1, 1)::int) IN (7, 8) THEN 'BÁSICA'
                WHEN (substr(curso.curso, 1, 1)::int) BETWEEN 1 AND 4 THEN 'MEDIA' END AS nivel,
                COUNT(matricula.id_estudiante) AS cantidad_estudiante
                FROM curso
                INNER JOIN matricula ON matricula.id_curso = curso.id_curso
                WHERE curso.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                AND matricula.id_estado = 1
                GROUP BY grado, nivel;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $grados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($grados as $grado) {
                $this->json['data'][] = $grado;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener los cursos por grado y la cantidad de estudiantes
        public function getDatosCurso($grado) {
            $query = "SELECT substr(curso.curso, 2, 2) AS letra_grado,
                COUNT(matricula.id_estudiante) AS cantidad_estudiante,
                COALESCE(INITCAP(split_part(docente.nombres_funcionario, ' ', 1) || ' ' || docente.ap_funcionario
                || ' ' || docente.am_funcionario), 'Sin datos') AS docente,
                COALESCE(INITCAP(split_part(paradocente.nombres_funcionario, ' ', 1) || ' ' || paradocente.ap_funcionario
                || ' ' || paradocente.am_funcionario), 'Sin datos') AS paradocente,
                COALESCE(INITCAP(split_part(inspectorGeneral.nombres_funcionario, ' ', 1) || ' ' || inspectorGeneral.ap_funcionario
                || ' ' || inspectorGeneral.am_funcionario), 'Sin datos') AS inspectorgeneral
                FROM curso
                LEFT JOIN matricula ON matricula.id_curso = curso.id_curso
                LEFT JOIN funcionario AS docente ON docente.id_funcionario = curso.id_docente_jefe
                LEFT JOIN funcionario AS paradocente ON paradocente.id_funcionario = curso.id_paradocente
                LEFT JOIN funcionario AS inspectorGeneral ON inspectorGeneral.id_funcionario = curso.id_inspector_general
                WHERE curso.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                AND matricula.id_estado = 1 AND (substr(curso.curso, 1, 1)::int) = ?
                GROUP BY letra_grado, docente, paradocente, inspectorGeneral
                ORDER BY letra_grado;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($grado)]);
            $cursos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach($cursos as $curso) {
                $this->json[] = $curso;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener la letra de los cursos según el grado
        public function loadLetra($grado) {
            $query = "SELECT id_curso, substr(curso, 2,2) AS curso FROM curso 
                WHERE curso LIKE ? AND anio_lectivo = EXTRACT (YEAR FROM now());";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$grado.'%']);
            $cursos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach($cursos as $curso) {
                $this->json[] = "<option value='".$curso['id_curso']."' >".$curso['curso']."</option>";
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener el número de lista correlativo por letra del curso
        public function getNumeroLista($id_curso) {
            $query = "SELECT COALESCE(numero_lista, 0) + 1 AS numero_lista
                FROM curso
                WHERE anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                AND id_curso = ?;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$id_curso]);
            $numero_lista = $sentencia->fetch();

            $this->closeConnection();
            return json_encode($numero_lista['numero_lista']);
        }



        // public function cargarLetrasGrado($grado) {
        //     $sentencia = $this->conexion_db->prepare($this->query_letras);
        //     $sentencia->execute([$grado.'%']);
        //     $cursos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        //     $option[] = "<option disable selected>Letra</option>";

        //     foreach ($cursos as $curso) {
        //         $option[] = "<option value='".$curso['id_curso']."' >".$curso['curso']."</option>";
        //     }

        //     return json_encode($option);
        //     $this->conexion_db = null;
        // }


        // protected $query_crear = "INSERT INTO cursos(curso) VALUES (?);";
        // // consulta que busca el curso correspondiente al año lectivo
        // protected $query_consultar = "SELECT curso FROM cursos WHERE curso LIKE ? AND anio_curso = EXTRACT(YEAR FROM NOW()) LIMIT 1;";
        // // recuperar las letras del grado
        // protected $query_letras = "SELECT id_curso, substr(curso, 2,2) as curso FROM curso WHERE curso LIKE ? AND anio_curso = EXTRACT(YEAR FROM NOW()) ORDER BY curso ASC;";
        // // private $json = array();

        // public function __construct() {
        //     parent:: __construct();
        // }

        // public function generarCurso($grado, $letraHasta) {
        //     foreach (LETRAS as $letra) {
        //         if ($letra <= $letraHasta) {
        //             $curso = $grado.$letra;
        //             $sentencia = $this->conexion_db->prepare($this->query_crear);
        //             $resultado = $sentencia->execute([$curso]);
        //         }
        //     }

        //     // DEVUELVE EL RESULTADO SI LA CREACIÓN A SIDO EXITOSA
        //     if ($resultado === true) {
        //         return json_encode(true);
        //     } else {
        //         return json_encode(false);
        //     }

        // }

        // public function consultarCurso($grado) {
        //     $sentencia = $this->conexion_db->prepare($this->query_consultar);
        //     $sentencia->execute([$grado.'%']);

        //     if ($sentencia->rowCount() >= 1) {
        //         return json_encode(false);
        //     } else {
        //         return json_encode(true);
        //     }

        // }

        // public function cargarLetrasGrado($grado) {
        //     $sentencia = $this->conexion_db->prepare($this->query_letras);
        //     $sentencia->execute([$grado.'%']);
        //     $cursos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        //     $option[] = "<option disable selected>Letra</option>";

        //     foreach ($cursos as $curso) {
        //         $option[] = "<option value='".$curso['id_curso']."' >".$curso['curso']."</option>";
        //     }

        //     return json_encode($option);
        //     $this->conexion_db = null;
        // }
    } 


/*     NOTA: PARA REINICIAR EL CONTADOR DEL CAMPO AUTOINCREMENTO
    alter sequence cursos_id_curso_seq restart with 1; */



?>