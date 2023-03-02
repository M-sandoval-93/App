<?php
    // Uso del modelo de conexión
    include_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    use PhpOffice\PhpSpreadsheet\Style\{Color, Font};


    class SuspensionEstudiante extends Conexion {
        public function __construct() {
            parent::__construct(); 
        }

        public function getCantidadSuspension() {
            $query = "SELECT SUM(CASE WHEN EXTRACT(YEAR FROM fecha_termino) = EXTRACT(YEAR FROM CURRENT_DATE) THEN 1 ELSE 0 END) AS cantidad_anual,
                SUM(CASE WHEN fecha_termino > CURRENT_DATE THEN 1 ELSE 0 END) AS cantidad_activa
                FROM suspension_estudiante
                WHERE EXTRACT(YEAR FROM fecha_termino) = EXTRACT(YEAR FROM CURRENT_DATE);";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute()) {
                $resultado = $sentencia->fetch();

                $this->json['cantidad_anual'] = $resultado['cantidad_anual'];
                $this->json['cantidad_activa'] = $resultado['cantidad_activa'];
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        public function getSuspension() {
            $query = "SELECT suspension_estudiante.id_suspension, (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                (estudiante.nombres_estudiante || ' ' || estudiante.ap_estudiante || ' ' || estudiante.am_estudiante) AS nombres,
                estudiante.nombre_social, curso.curso, to_char(suspension_estudiante.fecha_inicio, 'DD / MM / YYYY') AS fecha_inicio,
                to_char(suspension_estudiante.fecha_termino, 'DD / MM / YYYY') AS fecha_termino, suspension_estudiante.motivo,
                EXTRACT (DAY FROM AGE(suspension_estudiante.fecha_termino, suspension_estudiante.fecha_inicio)) + 1 AS dias_suspension,
                CASE WHEN suspension_estudiante.fecha_termino > CURRENT_DATE THEN 'Suspensión en curso' ELSE 'Suspensión terminada' END AS estado
                FROM suspension_estudiante
                INNER JOIN matricula ON matricula.id_matricula = suspension_estudiante.id_matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE EXTRACT(YEAR FROM suspension_estudiante.fecha_termino) = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY suspension_estudiante.fecha_termino DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $suspensiones = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($suspensiones as $suspension) {
                if ($suspension['nombre_social'] != '') {
                    $suspension['nombres'] = '('. $suspension['nombre_social']. ') '. $suspension['nombres']; 
                }
                $this->json['data'][] = $suspension;
                unset($this->json['data'][0]['nombre_social']); // Se elimina del array un dato innesesario
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        public function deleteSuspension($id_suspension) {
            $query = "UPDATE matricula SET id_estado = 1
                FROM suspension_estudiante
                WHERE suspension_estudiante.id_matricula = matricula.id_matricula
                AND suspension_estudiante.id_suspension = ?;";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([intval($id_suspension)])) {
                $query = "DELETE FROM suspension_estudiante WHERE id_suspension = ?;";

                $sentencia = $this->preConsult($query);
                if ($sentencia->execute([intval($id_suspension)])) {
                    $this->res = true;
                }
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

    }




?>