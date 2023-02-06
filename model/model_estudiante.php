<?php

    // Uso del modelo de conexión
    require_once "../model/model_conexion.php";

    class Estudiante extends Conexion {

        public function __construct() {
            parent:: __construct();
        }

        // Función para listar todos los estudiantes
        public function getEstudiantes() { // Terminado y revisado !!
            $query = "SELECT estudiante.id_estudiante,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) as rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante, estudiante.nombres_estudiante,
                estudiante.nombre_social, estudiante.junaeb, estudiante.fecha_retiro, matricula.anio_lectivo,
                to_char(estudiante.fecha_ingreso, 'DD / MM / YYYY') AS fecha_ingreso,
                to_char(estudiante.fecha_nacimiento, 'DD / MM / YYYY') AS fecha_nacimiento
                FROM estudiante
                LEFT JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                ORDER BY id_estudiante";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $estudiantes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach($estudiantes as $estudiante) {

                // Condición para asignar nombre social
                if ($estudiante['nombre_social'] != null) {
                    $estudiante['nombres_estudiante'] = '('. $estudiante['nombre_social']. ') '. $estudiante['nombres_estudiante']; 
                }

                // Condición para trabajar el estado del estudiante
                if ($estudiante['anio_lectivo'] == date("Y")) {
                    $estudiante['anio_lectivo'] = "Matriculado";
                } else {
                    $estudiante['anio_lectivo'] = "No matriculado";
                }

                $this->json['data'][] = $estudiante;
                unset($this->json['data'][0]['nombre_social']); // Se elimina del array un dato innesesario

            }

            $this->closeConnection();
            return json_encode($this->json);
            
        }

         // Función para obtener los datos de un estudiante
         public function getEstudiante($rut, $tipo) { // Terminado y revisado !!
            $query = "SELECT (estudiante.nombres_estudiante || ' ' || estudiante.ap_estudiante
                || ' ' || estudiante.am_estudiante) AS nombre_estudiante,
                estudiante.nombre_social, curso.curso, matricula.id_estado
                FROM estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE estudiante.rut_estudiante = ? AND matricula.anio_lectivo = EXTRACT(YEAR FROM now());";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$rut]);

            if ($this->json = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
                if ($this->json[0]['nombre_social'] != null) {
                    $this->json[0]['nombre_estudiante'] = '('.$this->json[0]['nombre_social']. ') '. $this->json[0]['nombre_estudiante'];
                }

                if ($tipo == "retraso") {
                    $queryCantidad = "SELECT count(atraso.id_atraso) AS cantidad_atraso
                        FROM atraso
                        INNER JOIN matricula ON matricula.id_matricula = atraso.id_matricula
                        INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                        WHERE estudiante.rut_estudiante = ? AND estado_atraso = 'sin justificar'
                        AND EXTRACT(YEAR FROM atraso.fecha_atraso) = EXTRACT(YEAR FROM now());";

                    $sentencia = $this->preConsult($queryCantidad);
                    $sentencia->execute([$rut]);

                    if ($cantidad_atraso = $sentencia->fetch()) {
                        $this->json[0]['cantidad_atraso'] = $cantidad_atraso['cantidad_atraso'];
                    }
                } else if ($tipo == 'justificacion') {
                    unset($this->json[0]['id_estado']);
                }

                unset($this->json[0]['nombre_social']);

                $this->closeConnection();
                return json_encode($this->json);
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        public function getCantidadEstudiante() {
            $query = "SELECT COUNT(id_estudiante) AS cantidad FROM estudiante;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $resultado = $sentencia->fetch();

            if ($resultado['cantidad'] >= 1) {
                $this->res = $resultado['cantidad'];
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        public function deleteEstudiante($id_estudiante) {
            $preQuery = "SELECT COUNT(id_estudiante) AS cantidad FROM matricula WHERE id_estudiante = ?;";
            $sentencia = $this->preConsult($preQuery);
            $sentencia->execute([$id_estudiante]);
            $resultado = $sentencia->fetch();

            if (!$resultado['cantidad'] >= 1) {
                $query = "DELETE FROM estudiante WHERE id_estudiante = ?;";
                $sentencia = $this->preConsult($query);
                $sentencia->execute([$id_estudiante]);
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        



        // AGREGAR NUEVO ESTUDIANTE
        // Revisar si se verifica que la matricula asignado no existe o que el rut del estudiante(id) no se encuentre ya ingresado
        // public function newEstudiante($e) {
        //     $query = "SELECT rut_estudiante FROM estudiante WHERE rut_estudiante = ?;";
        //     $sentencia = $this->conexion_db->prepare($query);
        //     $sentencia->execute([$e[2]]);

        //     if ($sentencia->rowCount() >= 1) {
        //         return json_encode('existe');

        //     } else {
        //         // AGREGAR DATOS A TABLA ESTUDIANTES
        //         $queryEstudiante = "INSERT INTO estudiante (rut_estudiante, dv_rut_estudiante, apellido_paterno_estudiante,
        //                 apellido_materno_estudiante, nombres_estudiante, nombre_social_estudiante, fecha_nacimiento_estudiante,
        //                 beneficio_junaeb, sexo_estudiante) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?);";
        //         $sentencia = $this->conexion_db->prepare($queryEstudiante);

        //         if ($sentencia->execute([$e[2], $e[3], $e[5], $e[6], $e[4], $e[7], $e[9], intval($e[11]), $e[10]])) {
                    
        //             // CONSULTAR EL ID ASIGNADO AL ESTUDIANTE
        //             $queryId = "SELECT id_estudiante FROM estudiante WHERE rut_estudiante = ?;";
        //             $sentencia = $this->conexion_db->prepare($queryId);
        //             $sentencia->execute([$e[2]]);
        //             $id_estudiante = $sentencia->fetch();

        //             // OBTENER ID APODERADOS
        //             $queryAp = "SELECT id_apoderado FROM apoderado WHERE rut_apoderado = ?;";
        //             $titular = null;
        //             $suplente = null;

        //             // ==================== OBTENER ID APODERADOS ======================= //
        //             if ($e[12] != '') { // OBTENER APODERADO TITULAR
        //                 $sentencia = $this->conexion_db->prepare($queryAp);
        //                 $sentencia->execute([$e[12]]);
        //                 $resultado = $sentencia->fetch();
        //                 $titular = $resultado["id_apoderado"];
        //             }

        //             if ($e[13] != '') { // OBTENER APODERADO SUPLENTE
        //                 $sentencia = $this->conexion_db->prepare($queryAp);
        //                 $sentencia->execute([$e[13]]);
        //                 $resultado = $sentencia->fetch();
        //                 $suplente = $resultado["id_apoderado"];
        //             }
        //             // ==================== OBTENER ID APODERADOS ======================= //

        //             // AGREGAR DATOS A TABLA MATRICULA
        //             $queryMatricula = "INSERT INTO matricula (numero_matricula, id_estudiante, id_apoderado_titular,
        //                     id_apoderado_suplente, id_curso, anio_lectivo, fecha_ingreso_estudiante) 
        //                     VALUES (?, ?, ?, ?, ?, ?, ?);";
        //             $sentencia = $this->conexion_db->prepare($queryMatricula);

        //             if ($sentencia->execute([$e[1], $id_estudiante["id_estudiante"], $titular, $suplente, $e[8], date("Y"), $e[0]])) {
        //                 $this->res = true;
        //             }
        //         }
        //     }

        //     $this->conexion_db = null;
        //     return json_encode($this->res);



        // }

        // EDITAR EL ESTADO DEL ESTUDIANTE
        // public function updateEstadoEstudiante($estudiante) {
        //     if ($estudiante[1] == 1) {
        //         $new_estado = 2;
        //     } else {
        //         $new_estado = 1;
        //     }

        //     $query = "UPDATE estudiante SET id_estado = ? WHERE id_estudiante = ?;";
        //     $sentencia = $this->conexion_db->prepare($query);

        //     if ($sentencia->execute([$new_estado, intval($estudiante[0])])) {
        //         $this->res = true;
        //     }

        //     $this->conexion_db = null;
        //     return json_encode($this->res);

        // }


       

    }




?>