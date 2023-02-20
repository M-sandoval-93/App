<?php

    // Uso del modelo de conexión
    require_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    
    class RetrasoEstudiante extends Conexion {

        public function __construct() {
            parent:: __construct();
        }

        public function getRetraso() { // Terminado y revisado !!
            $query = "SELECT atraso.id_atraso, (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                estudiante.nombres_estudiante AS nombre, estudiante.nombre_social AS n_social, curso.curso,
                to_char(atraso.fecha_atraso, 'DD/MM/YYYY') AS fecha_atraso,
                to_char(atraso.hora_atraso, 'HH:MI:SS') AS hora_atraso
                FROM atraso
                INNER JOIN matricula ON matricula.id_matricula = atraso.id_matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE EXTRACT(YEAR FROM atraso.fecha_atraso) = EXTRACT(YEAR FROM now())
                AND atraso.estado_atraso = 'sin justificar'
                ORDER BY atraso.fecha_atraso DESC, atraso.hora_atraso DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $atrasos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($atrasos as $atraso) {
                // condición para el nombre social
                if ($atraso['n_social'] != null) {
                    $atraso['nombre'] = '('. $atraso['n_social']. ') '. $atraso['nombre'];
                }
                $this->json['data'][] = $atraso;
                unset($this->json['data'][0]['n_social']); // Se elimina del array un dato innesesario
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        public function getRetrasoSinJustificar($rut) { // Terminado y Revisado !!
            $query = "SELECT atraso.id_atraso, to_char(atraso.fecha_atraso, 'DD/MM/YYYY') AS fecha_atraso,
                to_char(atraso.hora_atraso, 'HH:MI:SS') AS hora_atraso
                FROM matricula
                INNER JOIN atraso ON atraso.id_matricula = matricula.id_matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                WHERE estudiante.rut_estudiante = ? AND atraso.estado_atraso = 'sin justificar'
                AND EXTRACT(YEAR FROM atraso.fecha_atraso) = EXTRACT(YEAR FROM now())
                ORDER BY atraso.id_atraso DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$rut]);
            $atrasos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($atrasos as $atraso) {
                $this->json['data'][] = $atraso;
            }

            $this->closeConnection();
            return json_encode($this->json);

        }

        public function getCantidadRetraso($tipo) { // Terminado y revisado !!
            if ($tipo == 'diario') {
            // query que obtiene el atraso considerando; día, mes y año !!
                $query = "SELECT COUNT(id_atraso) AS atrasos FROM atraso 
                WHERE EXTRACT(DAY FROM fecha_atraso) = EXTRACT(DAY FROM CURRENT_DATE)
                AND EXTRACT(MONTH FROM fecha_atraso) = EXTRACT(MONTH FROM CURRENT_DATE)
                AND EXTRACT(YEAR FROM fecha_atraso) = EXTRACT(YEAR FROM CURRENT_DATE);";

            } else if ($tipo == 'total') {
                // query que obtiene el atraso considerando el año !!
                $query = "SELECT COUNT(id_atraso) AS atrasos FROM atraso
                WHERE EXTRACT(YEAR FROM fecha_atraso) = EXTRACT(YEAR FROM CURRENT_DATE);";
            }

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $resultado = $sentencia->fetch();

            if ($resultado['atrasos'] >= 1) {
                $this->res = $resultado['atrasos'];
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        public function setRetraso($rut) { // Terminado y revisado !!  --- ver si agrego en este apartado la impresión de ticket ----
            $queryE = "SELECT matricula.id_matricula
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                WHERE matricula.anio_lectivo = EXTRACT(YEAR FROM now())
                AND estudiante.rut_estudiante = ?;";

            $sentencia = $this->preConsult($queryE);

            if ($sentencia->execute([$rut])) {
                $idMatricula = $sentencia->fetch(PDO::FETCH_ASSOC);

                $query = "INSERT INTO atraso (fecha_hora_actual, fecha_atraso, hora_atraso, id_matricula)
                    VALUES (CURRENT_TIMESTAMP, CURRENT_DATE, CURRENT_TIME, ?);";

                $sentencia = $this->preConsult($query);
                if ($sentencia->execute([$idMatricula['id_matricula']])) {
                    $this->res = true;
                }
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        public function deleteRetraso($id_retraso) { // Terminado y revisado !!
            $query = "DELETE FROM atraso WHERE id_atraso = ?;";
            $sentencia = $this->preConsult($query);
            
            if ($sentencia->execute([$id_retraso])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        public function setJustificar($id_apoderado, $retrasos, $id_usuario) { // Terminado y revisado !!
            $query = "UPDATE atraso
                SET estado_atraso = 'justificado', id_usuario_justifica = ?, fecha_hora_justificacion = CURRENT_TIMESTAMP, id_apoderado_justifica = ?
                WHERE id_atraso = ?;";
            $sentencia = $this->preConsult($query);

            foreach ($retrasos as $retraso) {
                if (!$sentencia->execute([$id_usuario, $id_apoderado, $retraso])) {
                    $this->res = true;
                }
            }
            
            $this->closeConnection();
            return json_encode($this->res);

        }

        public function exportarRetraso($ext) { // Terminado y revisado !!
            $query = "SELECT (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                estudiante.nombres_estudiante AS nombre, estudiante.nombre_social AS n_social, curso.curso, 
                to_char(atraso.fecha_atraso, 'DD/MM/YYYY') AS fecha_atraso,
                to_char(atraso.hora_atraso, 'HH:MI:SS') AS hora_atraso,
                (apoderado.nombres_apoderado || ' ' || apoderado.ap_apoderado) as apoderado_justifica,
                atraso.estado_atraso
                FROM atraso
                INNER JOIN matricula ON matricula.id_matricula = atraso.id_matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                LEFT JOIN apoderado ON apoderado.id_apoderado = atraso.id_apoderado_justifica
                WHERE EXTRACT(YEAR FROM atraso.fecha_atraso) = EXTRACT(YEAR FROM now())
                ORDER BY atraso.fecha_atraso DESC, atraso.hora_atraso DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();            
            $atrasos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            $extension = 'Xlsx';

            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro atrasos');
            
            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Atrasos");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:I3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:I3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'REGISTRO ATRASO ESTUDIANTES');
            
            $sheetActive->getColumnDimension('A')->setWidth(15);
            $sheetActive->getColumnDimension('B')->setWidth(15);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(25);
            $sheetActive->getColumnDimension('E')->setWidth(10);
            $sheetActive->getColumnDimension('F')->setWidth(15);
            $sheetActive->getColumnDimension('G')->setWidth(15);
            $sheetActive->getColumnDimension('H')->setWidth(20);
            $sheetActive->getColumnDimension('I')->setWidth(30);
        
            $sheetActive->setCellValue('A3', 'RUT');
            $sheetActive->setCellValue('B3', 'AP PATERNO');
            $sheetActive->setCellValue('C3', 'AP MATERNO');
            $sheetActive->setCellValue('D3', 'NOMBRES');
            $sheetActive->setCellValue('E3', 'CURSO');
            $sheetActive->setCellValue('F3', 'FECHA');
            $sheetActive->setCellValue('G3', 'HORA');
            $sheetActive->setCellValue('H3', 'ESTADO ATRASO');
            $sheetActive->setCellValue('I3', 'APODERADO JUSTIFICA');

            $fila = 4;
            foreach ($atrasos as $atraso) {
                $sheetActive->setCellValue('A'.$fila, $atraso['rut']);
                $sheetActive->setCellValue('B'.$fila, $atraso['ap_paterno']);
                $sheetActive->setCellValue('C'.$fila, $atraso['ap_materno']);

                // Control de nombre social
                if ($atraso['n_social'] == '') {
                    $sheetActive->setCellValue('D'.$fila, $atraso['nombre']);
                } else {
                    $sheetActive->setCellValue('D'.$fila, '('.$atraso['n_social'].') '.$atraso['nombre']);
                }

                $sheetActive->setCellValue('E'.$fila, $atraso['curso']);
                $sheetActive->setCellValue('F'.$fila, $atraso['fecha_atraso']);
                $sheetActive->setCellValue('G'.$fila, $atraso['hora_atraso']);
                $sheetActive->setCellValue('H'.$fila, $atraso['estado_atraso']);
                $sheetActive->setCellValue('I'.$fila, $atraso['apoderado_justifica']);
                $fila++;
            }

            if ($ext == 'Csv') {
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
