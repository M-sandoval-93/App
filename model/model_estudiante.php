<?php

    // Uso del modelo de conexión
    require_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    use PhpOffice\PhpSpreadsheet\Style\{Color, Font};

    class Estudiante extends Conexion {
        public function __construct() {
            parent:: __construct();
        }

        // Método para listar todos los estudiantes
        public function getEstudiantes() {
            $query = "SELECT estudiante.id_estudiante,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) as rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante, 
                (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombres_estudiante,
                estudiante.junaeb, estudiante.fecha_retiro, estudiante.sexo,
                to_char(estudiante.fecha_ingreso, 'DD / MM / YYYY') AS fecha_ingreso,
                to_char(estudiante.fecha_nacimiento, 'DD / MM / YYYY') AS fecha_nacimiento,
                CASE WHEN matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE) AND matricula.id_estado = 1 THEN 'MATRICULADO'
                WHEN matricula.anio_lectivo < EXTRACT(YEAR FROM now()) AND matricula.anio_lectivo >= 2000 AND matricula.id_estado = 1 THEN 'AÑO ANTERIOR'
                WHEN matricula.id_estado = 4 THEN 'RETIRADO' WHEN matricula.id_estado = 5 THEN 'SUSPENDIDO' ELSE 'NO MATRICULADO' END AS estado
                FROM estudiante
                LEFT JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                ORDER BY estudiante.ap_estudiante;";


            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $estudiantes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach($estudiantes as $estudiante) {
                $this->json['data'][] = $estudiante;
            }

            $this->closeConnection();
            return json_encode($this->json);
            
        }

        public function comprobarEstudiante($rut) {
            $query = "SELECT id_estudiante FROM estudiante WHERE rut_estudiante = ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$rut]);

            if ($sentencia->fetchColumn() > 0) {
                $this->res = true;
            }

            $this->closeConnection();
            return $this->res;
        }

         // Método para obtener los datos de un estudiante o saber si existe en la bbdd
        public function getEstudiante($rut, $tipo) { // revisar, modificar y eliminar información obsoleta
            if ($tipo == 'existe') { // Condición para saber si el estudiante existe
                $this->res = $this->comprobarEstudiante($rut);
                return json_encode($this->res);
            }

            if ($tipo == 'existeMatricula') {
                $query = "SELECT estudiante.id_estudiante
                    FROM matricula
                    LEFT JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                    WHERE estudiante.rut_estudiante = ? AND matricula.anio_lectivo = EXTRACT(YEAR FROM now())";
                $sentencia = $this->preConsult($query);
                $sentencia->execute([$rut]);

                if ($sentencia->fetchColumn() > 0) {
                    $this->res = true;
                }

                $this->closeConnection();
                return json_encode($this->res);
            }

            if ($tipo == 'matricula') { // Condicion para obtener los datos del estudiante para ser matriculado
                $query = "SELECT (nombres_estudiante || ' ' || ap_estudiante || ' ' || am_estudiante) AS nombre_estudiante,
                    nombre_social FROM estudiante WHERE rut_estudiante = ?;";

                $sentencia = $this->preConsult($query);
                $sentencia->execute([$rut]);
                $estudiante = $sentencia->fetch();

                if ($estudiante['nombre_social'] != '') {
                    $estudiante['nombre_estudiante'] = '('. $estudiante['nombre_social']. ') '. $estudiante['nombre_estudiante'];
                }

                $this->closeConnection();
                return json_encode($estudiante['nombre_estudiante']);
            }

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

        // Método para obtener información del estudiante para registrar retraso
        public function getEstudianteRetraso($rut) {
            $query = "SELECT ((CASE WHEN e.nombre_social IS NULL THEN e.nombres_estudiante ELSE
                '(' || e.nombre_social || ') ' || e.nombres_estudiante END) || ' ' || e.ap_estudiante
                || ' ' || e.am_estudiante) AS nombre_estudiante, curso.curso, matricula.id_estado, 
                COALESCE(SUM(CASE WHEN retraso.estado_retraso = 'sin justificar'
                AND EXTRACT(year from retraso.fecha_retraso) = EXTRACT(YEAR FROM CURRENT_DATE) 
                THEN 1 ELSE 0 END), 0) AS cantidad_retraso
                FROM estudiante AS e
                INNER JOIN matricula ON matricula.id_estudiante = e.id_estudiante
                LEFT JOIN curso ON curso.id_curso = matricula.id_curso
                LEFT JOIN retraso ON retraso.id_estudiante = e.id_estudiante
                WHERE e.rut_estudiante = ? 
                AND matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                GROUP BY e.id_estudiante, curso.curso, matricula.id_estado;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$rut]);

            $this->json = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Función para obtener los datos de un estudiante a justificar
        public function getEstudianteJustificacion($rut) {
            $query = "SELECT (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante
                ELSE '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END
                || ' ' || estudiante.ap_estudiante
                || ' ' || estudiante.am_estudiante) AS nombre_estudiante, curso.curso
                FROM estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                LEFT JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE estudiante.rut_estudiante = ? AND matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE);";
            
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$rut]);
            $this->json = $sentencia->fetch(PDO::FETCH_ASSOC);

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener la cantidad de registros de estudiante
        public function getCantidadEstudiante() {
            $query = "SELECT COUNT(id_estudiante) AS cantidad_estudiante FROM estudiante;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $this->json = $sentencia->fetch(PDO::FETCH_ASSOC);

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para registrar un nuevo estudiante
        public function setEstudiante($e) {
            $query = "SELECT id_estudiante FROM estudiante WHERE rut_estudiante = ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$e->rut]);

            if ($sentencia->fetchColumn() > 0) {
                $this->closeConnection();
                return json_encode('existe');
            }

            $query = "INSERT INTO estudiante (rut_estudiante, dv_rut_estudiante, ap_estudiante, am_estudiante,
                nombres_estudiante, nombre_social, fecha_nacimiento, junaeb, sexo, fecha_ingreso) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$e->rut, $e->dv_rut, $e->ap, $e->am, $e->nombres, ($e->n_social == '') ? null : $e->n_social, $e->f_nacimiento, intval($e->junaeb), $e->sexo, $e->f_ingreso])) {
                $this->res = true;
            }
            
            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para actualiar el registro de un estudiante
        public function updateEstudiante($e) {
            $query = "UPDATE estudiante 
                SET rut_estudiante = ?, dv_rut_estudiante = ?, ap_estudiante = ?, am_estudiante = ?, nombres_estudiante = ?,
                nombre_social = ?, fecha_nacimiento = ?, junaeb = ?, sexo = ?, fecha_ingreso = ?
                WHERE id_estudiante = ?;";
            $sentencia = $this->preConsult($query);

            if ($sentencia->execute([$e->rut, $e->dv_rut, $e->ap, $e->am, $e->nombres, ($e->n_social == '') ? null : $e->n_social, $e->f_nacimiento, intval($e->junaeb), $e->sexo, $e->f_ingreso, intval($e->id_estudiante)])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para eliminar el registro de un estudiante
        public function deleteEstudiante($id_estudiante) {
            $preQuery = "SELECT COUNT(id_estudiante) AS cantidad FROM matricula WHERE id_estudiante = ?;";
            $sentencia = $this->preConsult($preQuery);
            $sentencia->execute([$id_estudiante]);
            $resultado = $sentencia->fetch();

            if (!$resultado['cantidad'] >= 1) {
                $query = "DELETE FROM estudiante WHERE id_estudiante = ?;";
                $sentencia = $this->preConsult($query);
                if ($sentencia->execute([$id_estudiante])) {
                    $this->res = true;
                }
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para exportar el registro de los estudiantes
        public function exportarEstudiantes($ext) {
            $extension = 'Xlsx';
            $query = "SELECT estudiante.id_estudiante, estudiante.sexo,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) as rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante, 
                (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombres_estudiante,
                estudiante.junaeb, estudiante.fecha_retiro, matricula.anio_lectivo,
                to_char(estudiante.fecha_ingreso, 'DD / MM / YYYY') AS fecha_ingreso,
                to_char(estudiante.fecha_nacimiento, 'DD / MM / YYYY') AS fecha_nacimiento
                FROM estudiante
                LEFT JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                ORDER BY id_estudiante;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();            
            $estudiantes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro estudiantes');
            
            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Estudiantes");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:J3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:J3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'REGISTRO ESTUDIANTES');
            
            $sheetActive->getColumnDimension('A')->setWidth(15);
            $sheetActive->getColumnDimension('B')->setWidth(18);
            $sheetActive->getColumnDimension('C')->setWidth(18);
            $sheetActive->getColumnDimension('D')->setWidth(32);
            $sheetActive->getColumnDimension('E')->setWidth(18);
            $sheetActive->getColumnDimension('F')->setWidth(8);
            $sheetActive->getColumnDimension('G')->setWidth(8);
            $sheetActive->getColumnDimension('H')->setWidth(18);
            $sheetActive->getColumnDimension('I')->setWidth(18);
            $sheetActive->getColumnDimension('J')->setWidth(22);       
            $sheetActive->getStyle('F:G')->getAlignment()->setHorizontal('center');

            $sheetActive->setCellValue('A3', 'RUT');
            $sheetActive->setCellValue('B3', 'AP PATERNO');
            $sheetActive->setCellValue('C3', 'AP MATERNO');
            $sheetActive->setCellValue('D3', 'NOMBRES');
            $sheetActive->setCellValue('E3', 'FECHA NACIMIENTO');
            $sheetActive->setCellValue('F3', 'SEXO');
            $sheetActive->setCellValue('G3', 'JUNAEB');
            $sheetActive->setCellValue('H3', 'FECHA INGRESO');
            $sheetActive->setCellValue('I3', 'FECHA RETIRO');
            $sheetActive->setCellValue('J3', 'ESTADO');

            $fila = 4;
            foreach ($estudiantes as $estudiante) {
                $sheetActive->setCellValue('A'.$fila, $estudiante['rut_estudiante']);
                $sheetActive->setCellValue('B'.$fila, $estudiante['ap_estudiante']);
                $sheetActive->setCellValue('C'.$fila, $estudiante['am_estudiante']);
                $sheetActive->setCellValue('D'.$fila, $estudiante['nombres_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $estudiante['fecha_nacimiento']);
                $sheetActive->setCellValue('F'.$fila, $estudiante['sexo']);
                $sheetActive->setCellValue('G'.$fila, $estudiante['junaeb']);
                $sheetActive->setCellValue('H'.$fila, $estudiante['fecha_ingreso']);
                $sheetActive->setCellValue('I'.$fila, $estudiante['fecha_retiro']);

                // Control del estado del estudiante, de acuerdo al anio lectivo
                if ($estudiante['anio_lectivo'] == date("Y")) {
                    $estudiante['anio_lectivo'] = 'Matriculado';
                } else {
                     $estudiante['anio_lectivo'] = 'No matriculado';
                     $sheetActive->getStyle('A'.$fila. ':J'.$fila)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                }

                $sheetActive->setCellValue('J'.$fila, $estudiante['anio_lectivo']);
                $fila++;
            }

            if ($ext == 'csv') {
                $extension = 'Csv';
            } 
            
            $writer = IOFactory::createWriter($file, $extension);

            ob_start();
            $writer->save('php://output');
            $documentData = ob_get_contents();
            ob_end_clean();

            $file = array ( "data" => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; base64,'.base64_encode($documentData));

            $this->closeConnection();
            return json_encode($file);
        }

    }

?>
