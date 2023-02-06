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

        public function showJustificacion() { // Terminado y revisado !!
            $query = "SELECT justificacion.id_justificacion, 
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                estudiante.nombres_estudiante AS nombre, estudiante.nombre_social AS n_social, curso.curso,
                to_char(justificacion.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
                to_char(justificacion.fecha_termino, 'DD/MM/YYYY') AS fecha_termino
                FROM justificacion
                INNER JOIN matricula ON matricula.id_matricula = justificacion.id_matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE matricula.anio_lectivo = EXTRACT(YEAR FROM now())
                ORDER BY justificacion.fecha_inicio DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();

            if ($justificaciones = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
                foreach ($justificaciones as $justificacion) {
                    // condición para el nombre social
                    if ($justificacion['n_social'] != null) {
                        $justificacion['nombre'] = '('. $justificacion['n_social']. ') '. $justificacion['nombre'];
                    }
                    $this->json['data'][] = $justificacion;
                    unset($this->json['data'][0]['n_social']); // Se elimina del array un dato innesesario
                }

                $this->closeConnection();
                return json_encode($this->json);
            }

            $this->closeConnection();
            return json_encode($this->res);
        }
        
        public function getJustificaciones() { // Terminado y revisado !!
            $query = "SELECT COUNT(id_justificacion) AS cantidad_justificacion FROM justificacion
                WHERE EXTRACT(YEAR FROM fecha_hora_actual) = EXTRACT(YEAR FROM CURRENT_DATE);";
            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $resultado = $sentencia->fetch();
        
            if ($resultado['cantidad_justificacion'] >= 1) {
                $this->res = $resultado['cantidad_justificacion'];
            }
        
            $this->closeConnection();
            return json_encode($this->res);
        }

        public function infoAdicional($id_registro) { // Terminado y revisado !!
            $query = "SELECT to_char(justificacion.fecha_hora_actual, 'DD/MM/YYY - HH:MI:SS') AS fecha_justificacion,
                (ap_titular.rut_apoderado || '-' || ap_titular.dv_rut_apoderado || ' / ' || ap_titular.ap_apoderado || ' ' || 
                ap_titular.am_apoderado || ' ' || ap_titular.nombres_apoderado) AS apoderado_titular,
                (ap_suplente.rut_apoderado || '-' || ap_suplente.dv_rut_apoderado || ' / ' || ap_suplente.ap_apoderado || ' ' || 
                ap_suplente.am_apoderado || ' ' || ap_suplente.nombres_apoderado) AS apoderado_suplente,
                justificacion.motivo_falta, justificacion.prueba_pendiente, justificacion.presenta_documento
                FROM justificacion
                INNER JOIN matricula ON matricula.id_matricula = justificacion.id_matricula
                LEFT JOIN apoderado AS ap_titular ON ap_titular.id_apoderado = matricula.id_ap_titular
                LEFT JOIN apoderado AS ap_suplente ON ap_suplente.id_apoderado = matricula.id_ap_suplente
                WHERE justificacion.id_justificacion = ?;";

            $queryPruebas = "SELECT asignatura.asignatura
                FROM prueba_pendiente
                INNER JOIN asignatura ON asignatura.id_asignatura = prueba_pendiente.id_asignatura
                WHERE prueba_pendiente.id_justificacion = ?;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$id_registro]);

            if ($this->json = $sentencia->fetchAll(PDO::FETCH_ASSOC)) {
                if ($this->json[0]['apoderado_suplente'] == null) {
                    $this->json[0]['apoderado'] = $this->json[0]['apoderado_titular'].(' (TITULAR)');
                } else {
                    $this->json[0]['apoderado'] = $this->json[0]['apoderado_suplente'].(' (SUPLENTE)');
                }

                unset($this->json[0]['apoderado_suplente']);
                unset($this->json[0]['apoderado_titular']);

                if ($this->json[0]['prueba_pendiente'] == true) {
                    $sentencia = $this->preConsult($queryPruebas);
                    $sentencia->execute([$id_registro]);
                    $asignaturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

                    foreach($asignaturas as $asignatura) {
                        foreach($asignatura as $nombre) {
                            $listaAsignaturas[] = $nombre;
                        }
                    }

                    $this->json[0]['asignatura'] = implode(' - ', $listaAsignaturas);
                }

                $this->closeConnection();
                return json_encode($this->json);
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        public function setJustificacion($e) { // Terminado y revisado !!
            $preQuery = "SELECT matricula.id_matricula
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                WHERE rut_estudiante = ?";

            $sentencia = $this->preConsult($preQuery);
            $sentencia->execute([$e[0]]);
            $matricula_estudiante = $sentencia->fetch();

            // Query para el ingreso de datos
            $query = "INSERT INTO justificacion (fecha_hora_actual, id_matricula, fecha_inicio, fecha_termino, id_apoderado,
                prueba_pendiente, presenta_documento, motivo_falta)
                VALUES (CURRENT_TIMESTAMP, ?, ?, ?, ?, ?, ?, ?);";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$matricula_estudiante['id_matricula'], $e[1], $e[2], intval($e[3]), $e[6], $e[5], $e[4]])) {

                if ($e[7] != "false") {
                    // Query para buscar el id_justificación recién ingrasado
                    $query = "SELECT MAX(id_justificacion) AS id_justificacion FROM justificacion;";
                    $sentencia = $this->preConsult($query);
                    $sentencia->execute();
                    $id_justificacion = $sentencia->fetch();
                    
                    // Query para insertar las asignaturas pendientes
                    $queryAsignatura = "INSERT INTO prueba_pendiente VALUES(?, ?);";
                    $sentencia = $this->preConsult($queryAsignatura);

                    foreach ($e[7] as $id_asignatura) {
                        $sentencia->execute([$id_justificacion['id_justificacion'], $id_asignatura]);
                    }
                }
                $this->res = true;   
            }

            return json_encode($this->res);
        }

        public function deleteJustificacion($id_justificacion) { // Terminado y revisado !!
            $queryJustificacion = "DELETE FROM justificacion WHERE id_justificacion = ?;";
            $queryAsignatura = "DELETE FROM prueba_pendiente WHERE id_justificacion = ?;";

            $sentencia = $this->preConsult($queryJustificacion);

            if ($sentencia->execute([$id_justificacion])) {
                $sentencia = $this->preConsult($queryAsignatura);
                $sentencia->execute([$id_justificacion]);
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        public function exportarJustificaciones($ext) { // Terminado y revisado !! !!
            $query = "SELECT (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                estudiante.nombres_estudiante AS nombre, estudiante.nombre_social AS n_social, curso.curso,
                to_char(justificacion.fecha_inicio, 'DD/MM/YYYY') AS fecha_inicio,
                to_char(justificacion.fecha_termino, 'DD/MM/YYYY') AS fecha_termino,
                (apoderado.nombres_apoderado || ' ' || apoderado.ap_apoderado) as apoderado_justifica,
                justificacion.motivo_falta, justificacion.prueba_pendiente, justificacion.presenta_documento,
                justificacion.id_justificacion
                FROM justificacion
                INNER JOIN matricula ON matricula.id_matricula = justificacion.id_matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                INNER JOIN apoderado ON apoderado.id_apoderado = justificacion.id_apoderado
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE EXTRACT(YEAR FROM justificacion.fecha_termino) = EXTRACT(YEAR FROM now())
                ORDER BY justificacion.fecha_termino DESC;";

            // Query para consultar las asignaturas si existen pruebas pendientes
            $queryPruebas = "SELECT asignatura.asignatura
                FROM prueba_pendiente
                INNER JOIN asignatura ON asignatura.id_asignatura = prueba_pendiente.id_asignatura
                WHERE prueba_pendiente.id_justificacion = ?;";

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

            $sheetActive->getColumnDimension('A')->setWidth(13);
            $sheetActive->getColumnDimension('A')->setWidth(15);
            $sheetActive->getColumnDimension('B')->setWidth(15);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(25);
            $sheetActive->getColumnDimension('E')->setWidth(10);
            $sheetActive->getColumnDimension('F')->setWidth(20);
            $sheetActive->getColumnDimension('G')->setWidth(20);
            $sheetActive->getColumnDimension('H')->setWidth(25);
            $sheetActive->getColumnDimension('I')->setWidth(30);
            $sheetActive->getColumnDimension('J')->setWidth(25);
            $sheetActive->getColumnDimension('K')->setWidth(30);
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
                $sheetActive->setCellValue('B'.$fila, $justificacion['ap_paterno']);
                $sheetActive->setCellValue('C'.$fila, $justificacion['ap_materno']);

                // Control de nombre social
                if ($justificacion['n_social'] == '') {
                    $sheetActive->setCellValue('D'.$fila, $justificacion['nombre']);
                } else {
                    $sheetActive->setCellValue('D'.$fila, '('.$justificacion['n_social'].') '.$justificacion['nombre']);
                }

                $sheetActive->setCellValue('E'.$fila, $justificacion['curso']);
                $sheetActive->setCellValue('F'.$fila, $justificacion['fecha_inicio']);
                $sheetActive->setCellValue('G'.$fila, $justificacion['fecha_termino']);
                $sheetActive->setCellValue('H'.$fila, $justificacion['apoderado_justifica']);
                $sheetActive->setCellValue('I'.$fila, $justificacion['motivo_falta']);
                $sheetActive->setCellValue('J'.$fila, $justificacion['presenta_documento']);

                // Control de pruebas pendientes
                if ($justificacion['prueba_pendiente'] == true) {
                    $sentencia = $this->preConsult($queryPruebas);
                    $sentencia->execute([$justificacion['id_justificacion']]);
                    $asignaturas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

                    foreach($asignaturas as $asignaturas) {
                        foreach($asignaturas as $nombre) {
                            $listaAsignaturas[] = $nombre;
                        }
                    }
                    $justificacion['prueba_pendiente'] = implode(' - ', $listaAsignaturas);
                }

                $sheetActive->setCellValue('K'.$fila, $justificacion['prueba_pendiente']);
                $fila++;
            }

            if ($ext == 'csv') {
                $extension = 'Csv';
            }

            // if ($ext == 'pdf') {
            //     $extension == 'Mpdf';  
            // }

            $writer = IOFactory::createWriter($file, $extension);

            ob_start();
            $writer->save('php://output');
            $documentData = ob_get_contents();
            ob_end_clean();

            $file = array ( "data" => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; base64,'.base64_encode($documentData));
            // $file = array ( "data" => 'data:application/pdf; base64,'.base64_encode($documentData));

            $this->closeConnection();
            return json_encode($file);





        }

        // public function getDocument() { // Trabajando y revisar errores
        //     // $templateWord = new TemplateProcessor('../Document/plantilla.docx');
        //     $templateWord = new TemplateProcessor('../Document/plantilla.docx');

        //     $nombre = "Mario Sandoval";

        //     $templateWord->setValue('nombre_prueba', $nombre);
        //     $templateWord->saveAs('Documento_01.docx');

        //     header("Content-Disposition: attachment; filename=Documento_01.docx; charset=iso-8859-1");
        //     $documento = file_get_contents("Documento_01.docx");

        //     return json_encode($documento);
            
        // }



    }

?>