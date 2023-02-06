<?php

    include_once "../model/model_conexion.php";

    class Apoderado extends Conexion {
        // private $json = array();

        public function __construct() {
            parent:: __construct();
        }

        // CONSULTAR LOS APODERADOS DE LA BASE DE DATOS
        public function consultaApoderados() {
            $query = "SELECT id_apoderado, (rut_apoderado || '-' || dv_rut_apoderado) as rut_apoderado, apellido_paterno_apoderado,
                        apellido_materno_apoderado, nombres_apoderado, estado_apoderado, telefono_apoderado FROM apoderado;";

            $sentencia = $this->conexion_db->prepare($query);
            $sentencia->execute();
            $apoderados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($apoderados as $apoderado) {
                $this->json['data'][] = $apoderado;
            }

            return json_encode($this->json);
            $this->conexion_db = null;
        }

        public function consultaApoderado($rut) {
            $query = "SELECT (nombres_apoderado || ' ' || apellido_paterno_apoderado || ' ' || apellido_materno_apoderado) AS nombre
            FROM apoderado WHERE rut_apoderado = ?;";

            $sentencia = $this->conexion_db->prepare($query);
            $sentencia->execute([$rut]);
            $apoderado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($apoderado as $ap) {
                $this->json[] = $ap['nombre'];
            }


            if ($sentencia->rowCount() >= 1) {
                return json_encode($this->json);
            } else {
                return json_encode(false);
            }

            $this->conexion_db = null;
        }

        // AGREGAR NUEVO APODERADO A LA BASE DE DATOS
        public function newApoderado($apoderado) {
            // VERIFICAR ANTES SI EL RUT DEL USUARIO EXISTE
            $query = "SELECT rut_apoderado FROM apoderado WHERE rut_apoderado = ?;";
            $sentencia = $this->conexion_db->prepare($query);
            $sentencia->execute([$apoderado[0]]);

            if ($sentencia->rowCount() >= 1) {
                return json_encode('existe');

            } else {
                $query = "INSERT INTO apoderado (rut_apoderado, dv_rut_apoderado, apellido_paterno_apoderado,
                apellido_materno_apoderado, nombres_apoderado, telefono_apoderado) VALUES (?, ?, ?, ?, ?, ?);";

                $sentencia = $this->conexion_db->prepare($query);
                $resultado = $sentencia->execute([$apoderado[0], $apoderado[1], $apoderado[3], $apoderado[4], $apoderado[2], '569-'.$apoderado[5]]);

                if ($resultado === true) {
                    return json_encode(true);
                } else {
                    return json_encode(false);
                }
            }

            $this->conexion_db = null;
        }

        // EDITAR LOS DATOS DEL APODERADO
        public function updateApoderado($apoderado) {
            $query = "UPDATE apoderado SET  apellido_paterno_apoderado = ?, apellido_materno_apoderado = ?,
                nombres_apoderado = ?, telefono_apoderado = ? WHERE rut_apoderado = ?;";

            $sentencia = $this->conexion_db->prepare($query);
            $resultado = $sentencia->execute([$apoderado[2], $apoderado[3], $apoderado[1], '569-'.$apoderado[4], $apoderado[0]]);

            if ($resultado === true) {
                return json_encode(true);
            } else {
                return json_encode(false);
            }

            $this->conexion_db = null;

        }

        // EDITAR EL ESTADO DEL APODERADO
        public function updateEstadoApoderado($id, $estado) {
            if ($estado == 'true') {
                $new_estado = 'false';
            } else {
                $new_estado = 'true';
            }

            $query = "UPDATE apoderado SET estado_apoderado = ? WHERE id_apoderado = ?;";
            $sentencia = $this->conexion_db->prepare($query);
            $resultado = $sentencia->execute([$new_estado, $id]);

            if ($resultado === true) {
                return json_encode(true);
            } else {
                return json_encode(false);
            }

            $this->conexion_db = null;
        }

        // ELIMINAR APODERADOS
        public function deleteApoderado($id) {
            $query = "DELETE FROM apoderado WHERE id_apoderado = ?;";
            $sentencia = $this->conexion_db->prepare($query);
            $resultado = $sentencia->execute([$id]);

            if ($resultado === true) {
                return json_encode(true);
            } else {
                return json_encode(false);
            }

            $this->conexion_db = null;
        }

        // Función utilizada para mostrar los apoderados que justifican en atrasos y justificaciones
        public function getApoderado($rut) {
            $query = "SELECT ap_titular.id_apoderado AS id_titular,
                (ap_titular.nombres_apoderado || ' ' || ap_titular.ap_apoderado || ' ' || ap_titular.am_apoderado) AS titular,
                ap_suplente.id_apoderado AS id_suplente,
                (ap_suplente.nombres_apoderado || ' ' || ap_suplente.ap_apoderado || ' ' || ap_suplente.am_apoderado) AS suplente
                FROM estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                LEFT JOIN apoderado AS ap_titular ON ap_titular.id_apoderado = matricula.id_ap_titular
                LEFT JOIN apoderado AS ap_suplente ON ap_suplente.id_apoderado = matricula.id_ap_suplente
                WHERE estudiante.rut_estudiante = ? AND matricula.anio_lectivo = EXTRACT(YEAR FROM now());";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$rut]);
            $datos = $sentencia->fetch(PDO::FETCH_ASSOC);
            $this->json[0] = "<option disable selected>Seleccionar apoderado</option>";

            if ($datos['id_titular'] != null && $datos['id_suplente'] != null) {
                $this->json[] = "<option value='".$datos['id_titular']."'>".$datos['titular'].'  ----  (TITULAR)'."</option>";
                $this->json[] = "<option value='".$datos['id_suplente']."'>".$datos['suplente'].'  ----  (SUPLENTE)'."</option>";

            } else if ($datos['id_titular'] != null && $datos['id_suplente'] == null) {
                $this->json[] = "<option value='".$datos['id_titular']."'>".$datos['titular'].'  ----  (TITULAR)'."</option>";

            } else if ($datos['id_titular'] == null && $datos['id_suplente'] != null) {
                $this->json[] = "<option value='".$datos['id_suplente']."'>".$datos['suplente'].'  ----  (SUPLENTE)'."</option>";

            } else {
                $this->json[0] = "<option disable selected>Sin apoderados !!</option>";

            }
            

            $this->closeConnection();
            return json_encode($this->json);

        }


    }



?>
