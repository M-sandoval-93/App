<?php
    // Uso del modelo de conexión
    include_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    // use PhpOffice\PhpSpreadsheet\Style\{Color, Font};


    class SuspensionEstudiante extends Conexion {
        public function __construct() {
            parent::__construct(); 
        }

        // Método para obtener el registro de suspensiones del año
        public function getSuspension() {
            $query = "SELECT suspension_estudiante.id_suspension, (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                ((CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) || ' ' || estudiante.ap_estudiante || ' ' || 
                estudiante.am_estudiante) AS nombres, curso.curso, to_char(suspension_estudiante.fecha_inicio, 'DD / MM / YYYY') AS fecha_inicio,
                to_char(suspension_estudiante.fecha_termino, 'DD / MM / YYYY') AS fecha_termino, suspension_estudiante.motivo,
                EXTRACT (DAY FROM AGE(suspension_estudiante.fecha_termino, suspension_estudiante.fecha_inicio)) + 1 AS dias_suspension,
                CASE WHEN suspension_estudiante.fecha_inicio < CURRENT_DATE AND suspension_estudiante.fecha_termino > CURRENT_DATE THEN 'Suspensión en curso' 
                WHEN suspension_estudiante.fecha_inicio > CURRENT_DATE AND suspension_estudiante.fecha_termino > CURRENT_DATE THEN 'Suspensión a comenzar'
                ELSE 'Suspensión terminada' END AS estado
                FROM suspension_estudiante
                INNER JOIN estudiante ON estudiante.id_estudiante = suspension_estudiante.id_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE EXTRACT(YEAR FROM suspension_estudiante.fecha_termino) = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY suspension_estudiante.fecha_inicio DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $suspensiones = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($suspensiones as $suspension) {
                $this->json['data'][] = $suspension;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener la cantidad de suspensión anuales y en curso
        public function getCantidadSuspension() {
            $query = "SELECT COALESCE(SUM(CASE WHEN EXTRACT(YEAR FROM fecha_termino) = EXTRACT(YEAR FROM CURRENT_DATE) THEN 1 ELSE 0 END), 0) AS cantidad_anual,
                COALESCE(SUM(CASE WHEN fecha_inicio < CURRENT_DATE AND fecha_termino > CURRENT_DATE THEN 1 ELSE 0 END), 0) AS cantidad_activa
                FROM suspension_estudiante
                WHERE EXTRACT(YEAR FROM fecha_termino) = EXTRACT(YEAR FROM CURRENT_DATE);";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $resultado = $sentencia->fetch();

            $this->json['cantidad_anual'] = $resultado['cantidad_anual'];
            $this->json['cantidad_activa'] = $resultado['cantidad_activa'];

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para actualizar el estado de una matricula si la fecha de suspensión ha terminado
        public function comprobarSuspension() {
            $query = "UPDATE matricula
                SET id_estado = 1
                WHERE id_matricula IN (
                    SELECT matricula.id_matricula
                    FROM matricula
                    INNER JOIN suspension_estudiante ON suspension_estudiante.id_estudiante = matricula.id_estudiante
                    WHERE suspension_estudiante.fecha_termino < CURRENT_DATE 
                    AND matricula.anio_lectivo = EXTRACT(year FROM CURRENT_DATE)
                    AND matricula.id_estado = 5
                );";
            
            $sentencia = $this->preConsult($query);
            if ($sentencia->execute()) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para eliminar un registro de suspensión y actualizar el estado de la matricula
        public function deleteSuspension($id_suspension) {
            $query = "UPDATE matricula SET id_estado = 1
                FROM suspension_estudiante
                WHERE suspension_estudiante.id_estudiante = matricula.id_estudiante
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

        // Método para descargar la información de las suspensiones
        public function exportarSuspension($ext) {
            $extension = 'Xlsx';
            $query = "SELECT (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut_estudiante,
                ((CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) || ' ' || estudiante.ap_estudiante || ' ' || 
                estudiante.am_estudiante) AS nombres_estudiante,
                curso.curso, suspension_estudiante.fecha_inicio, suspension_estudiante.fecha_termino, 
                EXTRACT (DAY FROM AGE(suspension_estudiante.fecha_termino, suspension_estudiante.fecha_inicio)) + 1 AS dias_suspension,
                suspension_estudiante.motivo
                FROM suspension_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = suspension_estudiante.id_estudiante
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE EXTRACT(YEAR FROM fecha_termino) = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY suspension_estudiante.fecha_inicio ASC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();            
            $suspensiones = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro Suspensiones');
            
            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Suspensiones");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:G3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:G3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'REGISTRO SUSPENSIÓN MATRICULAS, PERIODO '. date('Y'));
            
            $sheetActive->getColumnDimension('A')->setWidth(18);
            $sheetActive->getColumnDimension('B')->setWidth(50);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(18);
            $sheetActive->getColumnDimension('E')->setWidth(18);
            $sheetActive->getColumnDimension('F')->setWidth(10);
            $sheetActive->getColumnDimension('G')->setWidth(120);
               
            $sheetActive->getStyle('C')->getAlignment()->setHorizontal('center');
            $sheetActive->getStyle('F')->getAlignment()->setHorizontal('center');

            $sheetActive->setCellValue('A3', 'RUT');
            $sheetActive->setCellValue('B3', 'NOMBRES ESTUDIANTE');
            $sheetActive->setCellValue('C3', 'CURSO');
            $sheetActive->setCellValue('D3', 'FECHA INICIO');
            $sheetActive->setCellValue('E3', 'FECHA TÉRMINO');
            $sheetActive->setCellValue('F3', 'DÍAS');
            $sheetActive->setCellValue('G3', 'MOTIVO');
            

            $fila = 4;
            foreach ($suspensiones as $suspension) {
                $sheetActive->setCellValue('A'.$fila, $suspension['rut_estudiante']);
                $sheetActive->setCellValue('B'.$fila, $suspension['nombres_estudiante']);
                $sheetActive->setCellValue('C'.$fila, $suspension['curso']);
                $sheetActive->setCellValue('D'.$fila, $suspension['fecha_inicio']);
                $sheetActive->setCellValue('E'.$fila, $suspension['fecha_termino']);
                $sheetActive->setCellValue('F'.$fila, $suspension['dias_suspension']);
                $sheetActive->setCellValue('G'.$fila, $suspension['motivo']);
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