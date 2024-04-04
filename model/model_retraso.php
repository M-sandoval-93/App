<?php

    // Uso del modelo de conexión
    require_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    use PhpOffice\PhpSpreadsheet\Style\{Color, Font};
    
    class RetrasoEstudiante extends Conexion {

        public function __construct() {
            parent:: __construct();
        }
        // Método para obtener el registro de retrasos sin justificar
        public function getRetraso() {
            $query = "SELECT retraso.id_retraso, (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombre,
                curso.curso, to_char(retraso.fecha_retraso, 'DD/MM/YYYY') AS fecha_retraso,
                to_char(retraso.hora_retraso, 'HH:MI:SS') AS hora_retraso
                FROM retraso
                INNER JOIN estudiante ON estudiante.id_estudiante = retraso.id_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE EXTRACT(YEAR FROM retraso.fecha_retraso) = EXTRACT(YEAR FROM CURRENT_DATE)
                AND retraso.estado_retraso = 'sin justificar' AND matricula.id_estado != 4
                AND matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY retraso.fecha_retraso DESC, retraso.hora_retraso DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $retrasos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($retrasos as $retraso) {
                $this->json['data'][] = $retraso;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener los retrasos sin justificar de un estudiante
        public function getRetrasoSinJustificar($rut) {
            $query = "SELECT retraso.id_retraso, to_char(retraso.fecha_retraso, 'DD/MM/YYYY') AS fecha_retraso,
                to_char(retraso.hora_retraso, 'HH:MI:SS') AS hora_retraso
                FROM retraso
                INNER JOIN estudiante ON estudiante.id_estudiante = retraso.id_estudiante
                WHERE estudiante.rut_estudiante = ? AND retraso.estado_retraso = 'sin justificar'
                AND EXTRACT(YEAR FROM retraso.fecha_retraso) = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY retraso.fecha_retraso DESC, retraso.hora_retraso DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$rut]);
            $atrasos = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($atrasos as $atraso) {
                $this->json['data'][] = $atraso;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener la cantidad de retrasos diarios y anuales
        public function getCantidadRetraso() {
            $query = "SELECT COALESCE(SUM(CASE WHEN fecha_retraso = CURRENT_DATE THEN 1 ELSE 0 END), 0) AS cantidad_diaria,
                COALESCE(SUM(CASE WHEN EXTRACT(YEAR FROM fecha_retraso) = EXTRACT(YEAR FROM CURRENT_DATE) THEN 1 ELSE 0 END), 0) AS cantidad_anual
                FROM retraso
                WHERE EXTRACT(YEAR FROM fecha_retraso) = EXTRACT(YEAR FROM CURRENT_DATE);";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $resultado = $sentencia->fetch();

            $this->json['cantidad_diaria'] = $resultado['cantidad_diaria'];
            $this->json['cantidad_anual'] = $resultado['cantidad_anual'];

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para registrar el retraso de un estudiante
        public function setRetraso($rut, $id_usuario) {
            $query = "INSERT INTO retraso (fecha_hora_actual, fecha_retraso, hora_retraso, id_estudiante, id_usuario_registra)
                SELECT CURRENT_TIMESTAMP AS fecha_hora_actual, CURRENT_DATE AS fecha_retraso, CURRENT_TIME AS hora_retraso,
                id_estudiante, ?
                FROM estudiante 
                WHERE rut_estudiante = ?;";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$id_usuario, $rut])) {
                $this->res = true;

                $query = "SELECT retraso.id_retraso, curso.curso,
                    (CASE WHEN e.nombre_social IS NULL THEN e.nombres_estudiante || ' ' || e.ap_estudiante 
                    || ' ' || e.am_estudiante ELSE '(' || e.nombre_social || ') ' || e.nombres_estudiante 
                    || ' ' || e.ap_estudiante || ' ' || e.am_estudiante END) AS nombres,
                    to_char(retraso.fecha_retraso, 'DD/MM/YYYY') AS fecha_retraso,
                    to_char(retraso.hora_retraso, 'HH:MI:SS') AS hora_retraso
                    FROM estudiante e
                    INNER JOIN retraso ON retraso.id_estudiante = e.id_estudiante
                    INNER JOIN matricula ON matricula.id_estudiante = e.id_estudiante
                    INNER JOIN curso ON curso.id_curso = matricula.id_curso
                    WHERE retraso.id_retraso = (SELECT MAX(id_retraso) FROM retraso)
                    AND matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE);";
                
                $sentencia = $this->preConsult($query);
                $sentencia->execute();
                $this->json = $sentencia->fetch(PDO::FETCH_ASSOC);
            }
            $this->json['response'] = $this->res;

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para justificar uno o varios retrasos
        public function setJustificarRetraso($id_apoderado, $retrasos, $id_usuario) {
            $query = "UPDATE retraso SET estado_retraso = 'justificado', id_usuario_justifica = ?, 
                fecha_hora_justificacion = CURRENT_TIMESTAMP, id_apoderado_justifica = ?
                WHERE id_retraso = ?;";

            $sentencia = $this->preConsult($query);
            foreach ($retrasos as $retraso) {
                if ($sentencia->execute([intval($id_usuario), intval($id_apoderado), $retraso])) {
                    $this->res = true;
                }
            }
            
            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para eliminar el registro de un retraso
        public function deleteRetraso($id_retraso) {
            $query = "DELETE FROM retraso WHERE id_retraso = ?;";
            $sentencia = $this->preConsult($query);
            
            if ($sentencia->execute([intval($id_retraso)])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }
        
        // Método para exportar los retrasos del año
        public function exportarRetraso($ext) {
            $query = "SELECT (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombres,
                curso.curso, to_char(retraso.fecha_retraso, 'DD/MM/YYYY') AS fecha_retraso, 
                to_char(retraso.hora_retraso, 'HH:MI:SS') AS hora_retraso, 
                (funcionario.nombres_funcionario || ' ' || funcionario.ap_funcionario || ' ' || funcionario.am_funcionario) AS usuario_registra,
                (CASE WHEN matricula.id_estado != 1 THEN 'Retirado' ELSE retraso.estado_retraso END) AS estado_retraso,
                to_char(retraso.fecha_hora_justificacion, 'DD/MM/YYYY - HH:MM:SS') AS fecha_hora_justificacion,
                (apoderado.nombres_apoderado || ' ' || apoderado.ap_apoderado || ' ' || apoderado.am_apoderado) as apoderado_justifica,
                (fj.nombres_funcionario || ' ' || fj.ap_funcionario || ' ' || fj.am_funcionario) AS usuario_justifica
                FROM retraso
                INNER JOIN estudiante ON estudiante.id_estudiante = retraso.id_estudiante
                INNER JOIN matricula ON matricula.id_estudiante = estudiante.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                LEFT JOIN apoderado ON apoderado.id_apoderado = retraso.id_apoderado_justifica
                LEFT JOIN usuario AS uj ON uj.id_usuario = retraso.id_usuario_justifica
                LEFT JOIN funcionario AS fj ON fj.id_funcionario = uj.id_funcionario
                LEFT JOIN usuario ON usuario.id_usuario = retraso.id_usuario_registra
                LEFT JOIN funcionario ON funcionario.id_funcionario = usuario.id_funcionario
                WHERE EXTRACT(YEAR FROM retraso.fecha_retraso) = EXTRACT(YEAR FROM CURRENT_DATE)
                AND matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY retraso.fecha_retraso DESC, retraso.hora_retraso DESC;";

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
            $sheetActive->getStyle('A3:L3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:L3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'REGISTRO ATRASO ESTUDIANTES');
            
            $sheetActive->getColumnDimension('A')->setWidth(15);
            $sheetActive->getColumnDimension('B')->setWidth(15);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(25);
            $sheetActive->getColumnDimension('E')->setWidth(10);
            $sheetActive->getColumnDimension('F')->setWidth(15);
            $sheetActive->getColumnDimension('G')->setWidth(15);
            $sheetActive->getColumnDimension('H')->setWidth(30);
            $sheetActive->getColumnDimension('I')->setWidth(20);
            $sheetActive->getColumnDimension('J')->setWidth(25);
            $sheetActive->getColumnDimension('K')->setWidth(30);
            $sheetActive->getColumnDimension('L')->setWidth(30);
        
            $sheetActive->setCellValue('A3', 'RUT');
            $sheetActive->setCellValue('B3', 'PATERNO');
            $sheetActive->setCellValue('C3', 'MATERNO');
            $sheetActive->setCellValue('D3', 'NOMBRES');
            $sheetActive->setCellValue('E3', 'CURSO');
            $sheetActive->setCellValue('F3', 'FECHA RETRASO');
            $sheetActive->setCellValue('G3', 'HORA RETRASO');
            $sheetActive->setCellValue('H3', 'USUARIO REGISTRA');
            $sheetActive->setCellValue('I3', 'ESTADO RETRASO');
            $sheetActive->setCellValue('J3', 'FECHA/HORA JUSTIFICA');
            $sheetActive->setCellValue('K3', 'APODERADO JUSTIFICA');
            $sheetActive->setCellValue('L3', 'USUARIO JUSTIFICA');

            $fila = 4;
            foreach ($atrasos as $atraso) {
                $sheetActive->setCellValue('A'.$fila, $atraso['rut']);
                $sheetActive->setCellValue('B'.$fila, $atraso['ap_paterno']);
                $sheetActive->setCellValue('C'.$fila, $atraso['ap_materno']);
                $sheetActive->setCellValue('D'.$fila, $atraso['nombres']);
                $sheetActive->setCellValue('E'.$fila, $atraso['curso']);
                $sheetActive->setCellValue('F'.$fila, $atraso['fecha_retraso']);
                $sheetActive->setCellValue('G'.$fila, $atraso['hora_retraso']);
                $sheetActive->setCellValue('H'.$fila, $atraso['usuario_registra']);

                if ($atraso['estado_retraso'] == 'Retirado') {
                    $sheetActive->getStyle('A'.$fila.':L'.$fila)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                    $sheetActive->getStyle('A'.$fila.':L'.$fila)->getFont()->setStrikethrough(true);
                }
                $sheetActive->setCellValue('I'.$fila, $atraso['estado_retraso']);


                $sheetActive->setCellValue('J'.$fila, $atraso['fecha_hora_justificacion']);
                $sheetActive->setCellValue('K'.$fila, $atraso['apoderado_justifica']);
                $sheetActive->setCellValue('L'.$fila, $atraso['usuario_justifica']);
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
