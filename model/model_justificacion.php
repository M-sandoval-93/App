<?php

    require_once "../model/model_conexion.php";                         // Uso del modelo de conexión
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";           // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/openTBS/tbs_class.php";                   // Uso de librería para trabajar con word
    require_once "../Pluggins/openTBS/tbs_plugin_opentbs.php";          // Uso de librería para trabajar con word

    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};


    class JustificacionEstudiante extends Conexion {
        // Método constructor
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
                ('(' || CASE WHEN matricula.id_ap_titular = apoderado.id_apoderado THEN 'TITULAR'
                WHEN matricula.id_ap_suplente = apoderado.id_apoderado THEN 'SUPLENTE' 
                END || ') ' || apoderado.nombres_apoderado || ' ' || apoderado.ap_apoderado || ' ' ||
                apoderado.am_apoderado || ' / +569-' || apoderado.telefono) AS nombre_apoderado, justificacion.motivo_falta,
                CASE WHEN justificacion.prueba_pendiente = true THEN string_agg(asignatura.asignatura, ' - ')
                ELSE 'SIN PRUEBAS PENDIENTES' END AS prueba_pendiente,
                CASE WHEN justificacion.presenta_documento = true THEN 'PRESENTA DOCUMENTO' ELSE 'NO PRESENTA DOCUMENTO' END AS presenta_documento,
                CASE WHEN justificacion.informacion_verbal = true THEN 'SI' ELSE 'NO' END AS informacion_verbal,
                CASE WHEN tipo_documento_justificacion.tipo_documento IS NULL THEN 'SIN TIPO DE DOCUMENTO'
                ELSE tipo_documento_justificacion.tipo_documento END AS tipo_documento
                FROM justificacion
                INNER JOIN estudiante ON estudiante.id_estudiante = justificacion.id_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                INNER JOIN apoderado ON apoderado.id_apoderado = justificacion.id_apoderado
                LEFT JOIN prueba_pendiente ON prueba_pendiente.id_justificacion = justificacion.id_justificacion
                LEFT JOIN asignatura ON asignatura.id_asignatura = prueba_pendiente.id_asignatura
                LEFT JOIN tipo_documento_justificacion ON tipo_documento_justificacion.id_tipo_documento = justificacion.id_tipo_documento
                WHERE matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                GROUP BY justificacion.id_justificacion, rut, estudiante.ap_estudiante, estudiante.am_estudiante,
                nombres_estudiante, estudiante.nombre_social, curso.curso, fecha_inicio, fecha_termino,
                fecha_justificacion, nombre_apoderado, justificacion.motivo_falta, presenta_documento,
                tipo_documento_justificacion.tipo_documento
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

        // Método para obtener el tipo de documento con el que se justifica inasistencia
        public function getTipoDocumento() {
            $query = "SELECT * FROM tipo_documento_justificacion;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $tipos_documento = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            $this->json[0] = "<option selected value='0'>Presenta documento</option>";

            foreach($tipos_documento as $tipo_documento) {
                $this->json[] = "<option value='".$tipo_documento['id_tipo_documento']."' >".$tipo_documento['tipo_documento']."</option>";
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para registrar una justificacion
        public function setJustificacion($justificacion, $asignatura, $id_usser) {
            $query = "INSERT INTO justificacion (fecha_hora_actual, id_estudiante, fecha_inicio, 
                fecha_termino, id_apoderado, prueba_pendiente, presenta_documento, motivo_falta, informacion_verbal, id_usuario, id_tipo_documento)
                SELECT CURRENT_TIMESTAMP, id_estudiante, ?, ?, ?, ?, ?, ?, ?, ?, ?
                FROM estudiante
                WHERE rut_estudiante = ?;";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$justificacion->fecha_inicio, $justificacion->fecha_termino, intval($justificacion->id_apoderado), 
                $justificacion->pruebas, $justificacion->documento, $justificacion->motivo, $justificacion->info_verbal, $id_usser, 
                ($justificacion->id_tipo_documento == 0) ? null : $justificacion->id_tipo_documento, $justificacion->rut])) {

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
        public function getCertificadoJustificacion($id_justificacion) {
            $query = "SELECT (estudiante.nombres_estudiante || ' ' || estudiante.ap_estudiante || ' ' || estudiante.am_estudiante) AS estudiante,
                substring(curso.curso, 1, 1) AS grado, substring(curso.curso, 2, 2) AS letra,
                CASE WHEN (substring(curso.curso, 1, 1)::int) IN (7,8) THEN 'Básica'
                WHEN (substring(curso.curso, 1, 1)::int) BETWEEN 1 AND 4 THEN 'Media' END AS nivel,
                (apoderado.nombres_apoderado || ' ' || apoderado.ap_apoderado) AS apoderado,
                (apoderado.rut_apoderado || '-' || apoderado.dv_rut_apoderado) AS rut_ap,
                CASE WHEN justificacion.presenta_documento = true THEN 'SI - ' || tipo_documento_justificacion.tipo_documento
                ELSE 'NO SE PRESENTO DOCUMENTO' END AS documento,
                CASE WHEN justificacion.informacion_verbal = true THEN 'SI' ELSE 'NO' END AS info_verbal,
                CASE WHEN justificacion.prueba_pendiente = true THEN string_agg(asignatura.asignatura, ' - ')
                ELSE 'SIN PRUEBAS PENDIENTES' END AS prueba_pendiente, justificacion.motivo_falta,
                justificacion.exigencia, to_char(justificacion.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
                to_char(justificacion.fecha_termino, 'DD/MM/YYYY') AS fecha_termino,
                EXTRACT(YEAR FROM CURRENT_DATE) AS anio, EXTRACT(DAY FROM CURRENT_DATE) AS dia
                FROM justificacion
                INNER JOIN estudiante ON estudiante.id_estudiante = justificacion.id_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                INNER JOIN apoderado ON apoderado.id_apoderado = justificacion.id_apoderado
                LEFT JOIN prueba_pendiente ON prueba_pendiente.id_justificacion = justificacion.id_justificacion
                LEFT JOIN asignatura ON asignatura.id_asignatura = prueba_pendiente.id_asignatura
                LEFT JOIN tipo_documento_justificacion ON tipo_documento_justificacion.id_tipo_documento = justificacion.id_tipo_documento
                WHERE justificacion.id_justificacion = ?
                GROUP BY estudiante, curso, apoderado, rut_ap, documento, info_verbal, prueba_pendiente,
                motivo_falta, exigencia, fecha_inicio, fecha_termino, tipo_documento_justificacion.tipo_documento;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($id_justificacion)]);
            $this->json = $sentencia->fetch(PDO::FETCH_ASSOC);

            // Conseguir el mes actual en español
            $mes = array(
                'January' => 'ENERO',
                'February' => 'FEBRERO',
                'March' => 'MARZO',
                'April' => 'ABRIL',
                'May' => 'MAYO',
                'June' => 'JUNIO',
                'July' => 'JULIO',
                'August' => 'AGOSTO',
                'September' => 'SEPTIEMBRE',
                'October' => 'OCTUBRE',
                'November' => 'NOVIEMBRE',
                'December' => 'DICIEMBRE'
            );
            $mes_actual = date('F');

            // SECCIÓN PARA GENERAR EL WORD CON BASE EN UNA PLANTILLA DE WORD
            $TBS = new clsTinyButStrong; 
            $TBS->Plugin(TBS_INSTALL, OPENTBS_PLUGIN); 

            //Cargando template
            $template = '../docs/templateJustificacion.docx';
            $TBS->LoadTemplate($template, OPENTBS_ALREADY_UTF8);

            //Cargar valores
            $TBS->MergeField('pro.nombres', $this->json['estudiante']);
            $TBS->MergeField('pro.grado', $this->json['grado']);
            $TBS->MergeField('pro.letra', $this->json['letra']);
            $TBS->MergeField('pro.nivel', $this->json['nivel']);
            $TBS->MergeField('pro.apoderado', $this->json['apoderado']);
            $TBS->MergeField('pro.rut_apoderado', $this->json['rut_ap']);
            $TBS->MergeField('pro.documento', $this->json['documento']);
            $TBS->MergeField('pro.info_verbal', $this->json['info_verbal']);
            $TBS->MergeField('pro.prueba', $this->json['prueba_pendiente']);
            $TBS->MergeField('pro.motivo', $this->json['motivo_falta']);
            $TBS->MergeField('pro.exigencia', $this->json['exigencia']);
            $TBS->MergeField('pro.fecha_inicio', $this->json['fecha_inicio']);
            $TBS->MergeField('pro.fecha_termino', $this->json['fecha_termino']);
            $TBS->MergeField('pro.dia', $this->json['dia']);
            $TBS->MergeField('pro.mes', $mes[$mes_actual]);
            $TBS->MergeField('pro.anio', $this->json['anio']);


            $TBS->PlugIn(OPENTBS_DELETE_COMMENTS);

            $save_as = (isset($_POST['save_as']) && (trim($_POST['save_as'])!=='') && ($_SERVER['SERVER_NAME']=='localhost')) ? trim($_POST['save_as']) : '';
            $output_file_name = "Cerificado Justificacion.docx";
            
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
                    ('(' || CASE WHEN matricula.id_ap_titular = apoderado.id_apoderado THEN 'TITULAR'
                WHEN matricula.id_ap_suplente = apoderado.id_apoderado THEN 'SUPLENTE' 
                END || ') ' || apoderado.nombres_apoderado || ' ' || apoderado.ap_apoderado || ' ' ||
                apoderado.am_apoderado) AS nombre_apoderado, ('+569-' || apoderado.telefono) AS telefono, justificacion.motivo_falta,
                CASE WHEN justificacion.prueba_pendiente = true THEN string_agg(asignatura.asignatura, ' - ')
                ELSE 'SIN PRUEBAS PENDIENTES' END AS prueba_pendiente, justificacion.exigencia,
                CASE WHEN justificacion.presenta_documento = true THEN 'PRESENTA DOCUMENTO' ELSE 'NO PRESENTA DOCUMENTO' END AS presenta_documento,
                CASE WHEN justificacion.informacion_verbal = true THEN 'SI' ELSE 'NO' END AS informacion_verbal,
                CASE WHEN tipo_documento_justificacion.tipo_documento IS NULL THEN 'SIN TIPO DE DOCUMENTO'
                ELSE tipo_documento_justificacion.tipo_documento END AS tipo_documento,
                (funcionario.nombres_funcionario || ' ' || funcionario.ap_funcionario || ' ' || funcionario.am_funcionario) AS funcionario_registra
                FROM justificacion
                INNER JOIN estudiante ON estudiante.id_estudiante = justificacion.id_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                INNER JOIN apoderado ON apoderado.id_apoderado = justificacion.id_apoderado
                LEFT JOIN prueba_pendiente ON prueba_pendiente.id_justificacion = justificacion.id_justificacion
                LEFT JOIN asignatura ON asignatura.id_asignatura = prueba_pendiente.id_asignatura
                LEFT JOIN usuario ON usuario.id_usuario = justificacion.id_usuario
                LEFT JOIN funcionario ON funcionario.id_funcionario = usuario.id_funcionario
                LEFT JOIN tipo_documento_justificacion ON tipo_documento_justificacion.id_tipo_documento = justificacion.id_tipo_documento
                WHERE matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                GROUP BY justificacion.id_justificacion, rut, estudiante.ap_estudiante, estudiante.am_estudiante,
                nombres_estudiante, estudiante.nombre_social, curso.curso, fecha_inicio, fecha_termino,
                fecha_justificacion, nombre_apoderado, justificacion.motivo_falta, presenta_documento, funcionario_registra,
                apoderado.telefono, tipo_documento_justificacion.tipo_documento
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
            $sheetActive->getStyle('A3:Q3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:Q3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'REGISTRO JUSTIFICACIÓN ESTUDIANTES');

            $sheetActive->getColumnDimension('A')->setWidth(15);
            $sheetActive->getColumnDimension('B')->setWidth(15);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(30);
            $sheetActive->getColumnDimension('E')->setWidth(10);
            $sheetActive->getColumnDimension('F')->setWidth(20);
            $sheetActive->getColumnDimension('G')->setWidth(20);
            $sheetActive->getColumnDimension('H')->setWidth(30);
            $sheetActive->getColumnDimension('I')->setWidth(50);
            $sheetActive->getColumnDimension('J')->setWidth(20);
            $sheetActive->getColumnDimension('K')->setWidth(50);
            $sheetActive->getColumnDimension('L')->setWidth(30);
            $sheetActive->getColumnDimension('M')->setWidth(25);
            $sheetActive->getColumnDimension('N')->setWidth(15);
            $sheetActive->getColumnDimension('O')->setWidth(40);
            $sheetActive->getColumnDimension('P')->setWidth(15);
            $sheetActive->getColumnDimension('Q')->setWidth(30);
            $sheetActive->getStyle('N')->getAlignment()->setHorizontal('center');
            $sheetActive->getStyle('P')->getAlignment()->setHorizontal('center');

            $sheetActive->setCellValue('A3', 'RUT');
            $sheetActive->setCellValue('B3', 'AP PATERNO');
            $sheetActive->setCellValue('C3', 'AP MATERNO');
            $sheetActive->setCellValue('D3', 'NOMBRES');
            $sheetActive->setCellValue('E3', 'CURSO');
            $sheetActive->setCellValue('F3', 'FECHA INICIO');
            $sheetActive->setCellValue('G3', 'FECHA TÉRMINO');
            $sheetActive->setCellValue('H3', 'FECHA / HORA JUSTIFICACION');
            $sheetActive->setCellValue('I3', 'APODERADO JUSTIFICA');
            $sheetActive->setCellValue('J3', 'TELEFONO');
            $sheetActive->setCellValue('K3', 'MOTIVO FALTA');
            $sheetActive->setCellValue('L3', 'PRESENTA DOCUMENTO');
            $sheetActive->setCellValue('M3', 'TIPO DOCUMENTO');
            $sheetActive->setCellValue('N3', 'INFORMACION VERBAL');
            $sheetActive->setCellValue('O3', 'PRUEBA PENDIENTE');
            $sheetActive->setCellValue('P3', 'EXIGENCIA');
            $sheetActive->setCellValue('Q3', 'FUNCIONARIO REGISTRA');

            $fila = 4;
            foreach ($justificaciones as $justificacion) {
                $sheetActive->setCellValue('A'.$fila, $justificacion['rut']);
                $sheetActive->setCellValue('B'.$fila, $justificacion['ap_estudiante']);
                $sheetActive->setCellValue('C'.$fila, $justificacion['am_estudiante']);
                $sheetActive->setCellValue('D'.$fila, $justificacion['nombres_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $justificacion['curso']);
                $sheetActive->setCellValue('F'.$fila, $justificacion['fecha_inicio']);
                $sheetActive->setCellValue('G'.$fila, $justificacion['fecha_termino']);
                $sheetActive->setCellValue('H'.$fila, $justificacion['fecha_justificacion']);
                $sheetActive->setCellValue('I'.$fila, $justificacion['nombre_apoderado']);
                $sheetActive->setCellValue('J'.$fila, $justificacion['telefono']);
                $sheetActive->setCellValue('K'.$fila, $justificacion['motivo_falta']);
                $sheetActive->setCellValue('L'.$fila, $justificacion['presenta_documento']);
                $sheetActive->setCellValue('M'.$fila, $justificacion['tipo_documento']);
                $sheetActive->setCellValue('N'.$fila, $justificacion['informacion_verbal']);
                $sheetActive->setCellValue('O'.$fila, $justificacion['prueba_pendiente']);
                $sheetActive->setCellValue('P'.$fila, $justificacion['exigencia']. ' %');
                $sheetActive->setCellValue('Q'.$fila, $justificacion['funcionario_registra']);
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
