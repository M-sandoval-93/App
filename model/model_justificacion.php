<?php

    // Uso del modelo de conexión
    include_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};


    class JustificacionEstudiante extends Conexion {

        public function __construct() {
            parent:: __construct();
        }

        // Método para obtener la información de las justificaciones
        public function getJustificaciones() {
            $query = "SELECT justificacion.id_justificacion, (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante, estudiante.am_estudiante, 
                CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante
                ELSE '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END AS nombres_estudiante,
                curso.curso, to_char(justificacion.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
                to_char(justificacion.fecha_termino, 'DD/MM/YYYY') AS fecha_termino,
                to_char(justificacion.fecha_hora_actual, 'DD/MM/YYY - HH:MI:SS') AS fecha_justificacion,
                ('(' || CASE WHEN matricula.id_ap_titular = apoderado.id_apoderado THEN 'Titular'
                WHEN matricula.id_ap_suplente = apoderado.id_apoderado THEN 'Suplente' 
                END || ') ' || apoderado.nombres_apoderado || ' ' || apoderado.ap_apoderado || ' ' ||
                apoderado.am_apoderado) AS nombre_apoderado, justificacion.motivo_falta,
                CASE WHEN justificacion.prueba_pendiente = true THEN string_agg(asignatura.asignatura, ' - ')
                ELSE 'SIN PRUEBAS PENDIENTES' END AS prueba_pendiente,
                CASE WHEN justificacion.presenta_documento = true THEN 'SI' ELSE 'NO' END AS presenta_documento 
                FROM justificacion
                INNER JOIN estudiante ON estudiante.id_estudiante = justificacion.id_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                INNER JOIN apoderado ON apoderado.id_apoderado = justificacion.id_apoderado
                LEFT JOIN prueba_pendiente ON prueba_pendiente.id_justificacion = justificacion.id_justificacion
                LEFT JOIN asignatura ON asignatura.id_asignatura = prueba_pendiente.id_asignatura
                WHERE matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                GROUP BY justificacion.id_justificacion, rut, estudiante.ap_estudiante, estudiante.am_estudiante,
                nombres_estudiante, estudiante.nombre_social, curso.curso, fecha_inicio, fecha_termino,
                fecha_justificacion, nombre_apoderado, justificacion.motivo_falta, presenta_documento
                ORDER BY justificacion.fecha_inicio DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $justificaciones = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($justificaciones as $justificacion) {
                $this->json['data'][] = $justificacion;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener la cantidad de justificaciones anuales
        public function getCantidadJustificacion() {
            $query = "SELECT COUNT(id_justificacion) AS cantidad_justificacion
                FROM justificacion
                WHERE EXTRACT(YEAR FROM fecha_hora_actual) = EXTRACT(YEAR FROM CURRENT_DATE)";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $this->json = $sentencia->fetch(PDO::FETCH_ASSOC);
            
            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para registrar una justificacion
        public function setJustificacion($justificacion, $asignatura) {
            $query = "INSERT INTO justificacion (fecha_hora_actual, id_estudiante, fecha_inicio, 
                fecha_termino, id_apoderado, prueba_pendiente, presenta_documento, motivo_falta)
                SELECT CURRENT_TIMESTAMP, id_estudiante, ?, ?, ?, ?, ?, ?
                FROM estudiante
                WHERE rut_estudiante = ?;";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$justificacion->fecha_inicio, $justificacion->fecha_termino, intval($justificacion->id_apoderado), 
            $justificacion->pruebas, $justificacion->documento, $justificacion->motivo, $justificacion->rut])) {

                if ($justificacion->pruebas == true) {
                    $query = "INSERT INTO prueba_pendiente (id_justificacion, id_asignatura) 
                        SELECT MAX(id_justificacion), ?
                        FROM justificacion;";
                    
                    $sentencia = $this->preConsult($query);
                    foreach ($asignatura as $id_asignatura) {
                        $sentencia->execute([$id_asignatura]);
                    }
                }

                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para eliminar el registro de una justificación
        public function deleteJustificacion($id_justificacion) {
            $queryJustificacion = "DELETE FROM justificacion WHERE id_justificacion = ?;";
            $queryAsignatura = "DELETE FROM prueba_pendiente WHERE id_justificacion = ?;";

            $sentencia = $this->preConsult($queryJustificacion);

            if ($sentencia->execute([intval($id_justificacion)])) {
                $sentencia = $this->preConsult($queryAsignatura);
                $sentencia->execute([intval($id_justificacion)]);
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para generar certificado de justificación
        public function getCertificadoJustificaicon($id_justificacion) {
            $query = "SELECT matricula.matricula, (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                (estudiante.nombres_estudiante || ' ' || estudiante.ap_estudiante || ' ' || estudiante.am_estudiante) AS nombres,
                substring(curso.curso, 1, 1) AS grado, substring(curso.curso, 2, 2) AS letra,
                CASE WHEN (substring(curso.curso, 1, 1)::int) IN (7,8) THEN 'Básica'
                WHEN (substring(curso.curso, 1, 1)::int) BETWEEN 1 AND 4 THEN 'Media' END AS nivel,
                EXTRACT(YEAR FROM CURRENT_DATE) AS anio, EXTRACT(DAY FROM CURRENT_DATE) AS dia
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE matricula.id_matricula = ?;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($id_matricula)]);
            $this->json = $sentencia->fetch(PDO::FETCH_ASSOC);

            // Conseguir el mes actual en español
            $mes = array(
                'January' => 'Enero',
                'February' => 'Febrero',
                'March' => 'Marzo',
                'April' => 'Abril',
                'May' => 'Mayo',
                'June' => 'Junio',
                'July' => 'Julio',
                'August' => 'Agosto',
                'September' => 'Septiembre',
                'October' => 'Octubre',
                'November' => 'Noviembre',
                'December' => 'Diciembre'
            );
            $mes_actual = date('F');

            // SECCIÓN PARA GENERAR EL WORD CON BASE EN UNA PLANTILLA DE WORD
            $TBS = new clsTinyButStrong; 
            $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); 

            //Cargando template
            $template = '../docs/templateCertificado.docx';
            $TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            //Cargar valores
            $TBS->MergeField('pro.nombres', $this->json['nombres']);
            $TBS->MergeField('pro.rut', $this->json['rut']);
            $TBS->MergeField('pro.grado', $this->json['grado']);
            $TBS->MergeField('pro.letra', $this->json['letra']);
            $TBS->MergeField('pro.nivel', $this->json['nivel']);
            $TBS->MergeField('pro.anio_1', $this->json['anio']);
            $TBS->MergeField('pro.matricula', $this->json['matricula']);
            $TBS->MergeField('pro.mes', $mes[$mes_actual]);
            $TBS->MergeField('pro.dia', $this->json['dia']);
            $TBS->MergeField('pro.anio_2', $this->json['anio']);


            $TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

            $save_as = (isset($_POST['save_as']) && (trim($_POST['save_as'])!=='') && ($_SERVER['SERVER_NAME']=='localhost')) ? trim($_POST['save_as']) : '';
            $output_file_name = "Cerificado Alumno Regular_". $this->json['rut'] .".docx";
            
            if ($save_as==='') {
                $TBS->Show(OPENTBS_DOWNLOAD, $output_file_name); 
                exit();
            } 
        }

        // Método para generar excel de las justificaciones
        public function exportarJustificaciones($ext) {
            $query = "SELECT justificacion.id_justificacion, (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante, estudiante.am_estudiante, 
                CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante
                ELSE '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END AS nombres_estudiante,
                curso.curso, to_char(justificacion.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
                to_char(justificacion.fecha_termino, 'DD/MM/YYYY') AS fecha_termino,
                to_char(justificacion.fecha_hora_actual, 'DD/MM/YYY - HH:MI:SS') AS fecha_justificacion,
                ('(' || CASE WHEN matricula.id_ap_titular = apoderado.id_apoderado THEN 'Titular'
                WHEN matricula.id_ap_suplente = apoderado.id_apoderado THEN 'Suplente' 
                END || ') ' || apoderado.nombres_apoderado || ' ' || apoderado.ap_apoderado || ' ' ||
                apoderado.am_apoderado) AS nombre_apoderado, justificacion.motivo_falta,
                CASE WHEN justificacion.prueba_pendiente = true THEN string_agg(asignatura.asignatura, ' - ')
                ELSE 'SIN PRUEBAS PENDIENTES' END AS prueba_pendiente,
                CASE WHEN justificacion.presenta_documento = true THEN 'SI' ELSE 'NO' END AS presenta_documento 
                FROM justificacion
                INNER JOIN estudiante ON estudiante.id_estudiante = justificacion.id_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                INNER JOIN apoderado ON apoderado.id_apoderado = justificacion.id_apoderado
                LEFT JOIN prueba_pendiente ON prueba_pendiente.id_justificacion = justificacion.id_justificacion
                LEFT JOIN asignatura ON asignatura.id_asignatura = prueba_pendiente.id_asignatura
                WHERE matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                GROUP BY justificacion.id_justificacion, rut, estudiante.ap_estudiante, estudiante.am_estudiante,
                nombres_estudiante, estudiante.nombre_social, curso.curso, fecha_inicio, fecha_termino,
                fecha_justificacion, nombre_apoderado, justificacion.motivo_falta, presenta_documento
                ORDER BY justificacion.fecha_inicio DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();            
            $justificaciones = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            $extension = 'Xlsx';

            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro justificaciones');

            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Justificaciones");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:K3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:K3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'REGISTRO JUSTIFICACIÓN ESTUDIANTES');

            $sheetActive->getColumnDimension('A')->setWidth(15);
            $sheetActive->getColumnDimension('B')->setWidth(15);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(25);
            $sheetActive->getColumnDimension('E')->setWidth(10);
            $sheetActive->getColumnDimension('F')->setWidth(20);
            $sheetActive->getColumnDimension('G')->setWidth(20);
            $sheetActive->getColumnDimension('H')->setWidth(40);
            $sheetActive->getColumnDimension('I')->setWidth(50);
            $sheetActive->getColumnDimension('J')->setWidth(25);
            $sheetActive->getColumnDimension('K')->setWidth(35);
            $sheetActive->getStyle('J:K')->getAlignment()->setHorizontal('left');

            $sheetActive->setCellValue('A3', 'RUT');
            $sheetActive->setCellValue('B3', 'AP PATERNO');
            $sheetActive->setCellValue('C3', 'AP MATERNO');
            $sheetActive->setCellValue('D3', 'NOMBRES');
            $sheetActive->setCellValue('E3', 'CURSO');
            $sheetActive->setCellValue('F3', 'FECHA INICIO');
            $sheetActive->setCellValue('G3', 'FECHA TÉRMINO');
            $sheetActive->setCellValue('H3', 'APODERADO JUSTIFICA');
            $sheetActive->setCellValue('I3', 'MOTIVO FALTA');
            $sheetActive->setCellValue('J3', 'PRESENTA DOCUMENTO');
            $sheetActive->setCellValue('K3', 'PRUEBA PENDIENTE');

            $fila = 4;
            foreach ($justificaciones as $justificacion) {
                $sheetActive->setCellValue('A'.$fila, $justificacion['rut']);
                $sheetActive->setCellValue('B'.$fila, $justificacion['ap_estudiante']);
                $sheetActive->setCellValue('C'.$fila, $justificacion['am_estudiante']);
                $sheetActive->setCellValue('D'.$fila, $justificacion['nombres_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $justificacion['curso']);
                $sheetActive->setCellValue('F'.$fila, $justificacion['fecha_inicio']);
                $sheetActive->setCellValue('G'.$fila, $justificacion['fecha_termino']);
                $sheetActive->setCellValue('H'.$fila, $justificacion['nombre_apoderado']);
                $sheetActive->setCellValue('I'.$fila, $justificacion['motivo_falta']);
                $sheetActive->setCellValue('J'.$fila, $justificacion['presenta_documento']);
                $sheetActive->setCellValue('K'.$fila, $justificacion['prueba_pendiente']);
                $fila++;
            }

            if ($ext == 'csv') { $extension = 'Csv'; }

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
