<?php

    include_once "../model/model_conexion.php";

    class InasistenciaFuncionario extends Conexion {
        private $json = array();
        private $res = false;

        public function __construct() {
            parent:: __construct();
        }

        public function consultarInasistenciaF() { // LISTO
            $query = "SELECT inasistencia_funcionario.id_inasistencia, 
                    (reemplazante.nombres_funcionario || ' ' || reemplazante.apellido_paterno_funcionario || ' ' || reemplazante.apellido_materno_funcionario) AS reemplazante,
                    (funcionario.nombres_funcionario || ' ' || funcionario.apellido_paterno_funcionario || ' ' || funcionario.apellido_materno_funcionario) AS funcionario,
                    to_char(inasistencia_funcionario.fecha_inicio, 'dd / mm / yyyy') as fecha_inicio,
                    to_char(inasistencia_funcionario.fecha_termino, 'dd / mm / yyyy') as fecha_termino,
                    inasistencia_funcionario.dias_inasistencia, 
                    tipo_inasistencia.tipo_inasistencia,
                    inasistencia_funcionario.ordinario
                    FROM inasistencia_funcionario
                    LEFT JOIN funcionario as reemplazante ON reemplazante.id_funcionario = inasistencia_funcionario.id_reemplazante
                    LEFT JOIN funcionario ON funcionario.id_funcionario = inasistencia_funcionario.id_funcionario
                    LEFT JOIN tipo_inasistencia ON tipo_inasistencia.id_tipo_inasistencia = inasistencia_funcionario.id_tipo_inasistencia
                    WHERE EXTRACT(YEAR FROM fecha_termino) = EXTRACT(YEAR FROM NOW());";

            $sentencia = $this->conexion_db->prepare($query);
            $sentencia->execute();
            $inasistencias = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($inasistencias as $inasistencia) {
                $this->json['data'][] = $inasistencia;
            }

            return json_encode($this->json);
            $this->conexion_db = null;

        }

        public function buscarFuncionario($rut) { // LISTO
            $query = "SELECT (nombres_funcionario || ' ' || apellido_paterno_funcionario || ' ' || apellido_materno_funcionario) as nombre_funcionario
                    FROM funcionario WHERE rut_funcionario = ?;";
            $sentencia = $this->conexion_db->prepare($query);
            $sentencia->execute([$rut]);

            if ($sentencia->rowCount() >=1) {
                $funcionario = $sentencia->fetch(PDO::FETCH_ASSOC);
                return json_encode($funcionario['nombre_funcionario']);
            } else {
                return json_encode($this->res);
            }

            $this->conexion_db = null;
        }

        public function newInaistenciaF($iF) {  // LISTO
            $query = "INSERT INTO inasistencia_funcionario (id_funcionario, fecha_inicio, fecha_termino, 
                    dias_inasistencia, id_tipo_inasistencia, id_reemplazante, ordinario) VALUES (?, ?, ?, ?, ?, ?, ?);";
            
            $queryF = "SELECT id_funcionario FROM funcionario WHERE rut_funcionario = ?;";
            $sentencia = $this->conexion_db->prepare($queryF);

            if ($sentencia->execute([$iF[1]])) {

                // AGREGAR VALIDACIÓN PARA SABER SI EXISTE EL RUT INGRESADO, TANTO EN FUNCIONARIO COMO EN REEMPLAZO !!!! !!!!!!! !!!!!!!!! !!!!!!!!
                $resultadoF = $sentencia->fetch();
    
                if ($iF[6] != '') {
                    $sentencia = $this->conexion_db->prepare($queryF);
                    $sentencia->execute([$iF[6]]);
                    $resultadoR = $sentencia->fetch();
                }

                $sentencia = $this->conexion_db->prepare($query);
                if ($sentencia->execute([$resultadoF["id_funcionario"], $iF[2], $iF[3], $iF[4], $iF[0], (isset($resultadoR["id_funcionario"])) ? $resultadoR["id_funcionario"] : null, $iF[5]])) {
                    $this->res = true;
                }
            }

            return json_encode($this->res);
            $this->conexion_db = null;

        }

        public function getInasistencia($id_inasistencia) { // LISTO
            $query = "SELECT inasistencias.id_inasistencia,
                inasistencias.id_tipo_inasistencia AS inasistencia, inasistencias.ordinario,
                inasistencias.fecha_inicio, inasistencias.fecha_termino,
                funcionario.rut_funcionario AS r_funcionario,
                rreemplazo.rut_funcionario AS r_reemplazo,
                inasistencias.dias_inasistencia
                FROM inasistencia_funcionario AS inasistencias
                LEFT JOIN funcionario ON funcionario.id_funcionario = inasistencias.id_funcionario
                LEFT JOIN funcionario AS rfuncionario ON rfuncionario.id_funcionario = inasistencias.id_funcionario
                LEFT JOIN funcionario AS rreemplazo ON rreemplazo.id_funcionario = inasistencias.id_reemplazante
                WHERE id_inasistencia = ?;"; 
            $sentencia = $this->conexion_db->prepare($query);
            $sentencia->execute([$id_inasistencia]);

            if ($sentencia->rowCount() >=1) {
                $inasistencias = $sentencia->fetchAll(PDO::FETCH_ASSOC);
                foreach ($inasistencias as $inasistencia) {
                    $this->json[] = $inasistencia;
                }
                return json_encode($this->json);

            } else {
                return json_encode($this->res);
            }
            $this->conexion_db = null;
        }

        public function updateInasistenciaF($iF) { // LISTO
            $query = "UPDATE inasistencia_funcionario SET fecha_inicio = ?, fecha_termino = ?, dias_inasistencia = ?, id_tipo_inasistencia = ?,
                    id_reemplazante = ?, ordinario = ? WHERE id_inasistencia = ?;";

            $queryF = "SELECT id_funcionario FROM funcionario WHERE rut_funcionario = ?;";
            $sentencia = $this->conexion_db->prepare($queryF);

            if ($sentencia->execute([$iF[6]])) {
                $resultadoR = $sentencia->fetch();

                $sentencia = $this->conexion_db->prepare($query);
                if ($sentencia->execute([$iF[2], $iF[3], $iF[4], $iF[0], (isset($resultadoR["id_funcionario"])) ? $resultadoR["id_funcionario"] : null, $iF[5], $iF[7]])) {
                    $this->res = true;
                }

            }

            return json_encode($this->res);
            $this->conexion_db = null;
        }

        public function deleteInasistenciaF($id) { // LISTO
            $query = "DELETE FROM inasistencia_funcionario WHERE id_inasistencia = ?;";
            $sentencia = $this->conexion_db->prepare($query);

            if ($sentencia->execute([$id])) {
                $this->res = true;
            }

            return json_encode($this->res);
            $this->conexion_db = null;
        }

        public function getTipoInasistencia() { // LISTO
            $query = "SELECT * FROM tipo_inasistencia;";
            $sentencia = $this->conexion_db->prepare($query);
            $sentencia->execute();
            $inasistencia = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            $option[] = "<option disable selected>SELECCIONAR TIPO</option>";

            foreach ($inasistencia as $i) {
                $option[] = "<option value='".$i['id_tipo_inasistencia']."' >".$i['tipo_inasistencia']."</option>";
            }

            return json_encode($option);
            $this->conexion_db = null;

        }
    }


?>
