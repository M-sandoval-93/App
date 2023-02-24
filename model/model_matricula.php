<?php

    // Uso del modelo de conexión
    include_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    use PhpOffice\PhpSpreadsheet\Style\{Color, Font};


    class MatriculaEstudiantes extends Conexion {

        public function __construct() {
            parent::__construct(); 
        }

        // Método para obtener datos de las matrículas
        public function gerMatricula() {
            $query = "SELECT matricula.id_matricula, matricula.matricula,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                estudiante.nombres_estudiante AS nombre, estudiante.nombre_social AS n_social, curso.curso,
                to_char(estudiante.fecha_nacimiento, 'DD / MM / YYYY') AS fecha_nacimiento,
                to_char(estudiante.fecha_ingreso, 'DD / MM / YYYY') AS fecha_ingreso,
                estudiante.sexo, estado.nombre_estado,
                (apt.nombres_apoderado || ' ' || apt.ap_apoderado || ' ' || apt.am_apoderado) AS apoderado_titular,
                (aps.nombres_apoderado || ' ' || aps.ap_apoderado || ' ' || aps.am_apoderado) AS apoderado_suplente
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                LEFT JOIN curso ON curso.id_curso = matricula.id_curso
                LEFT JOIN estado ON estado.id_estado = matricula.id_estado
                LEFT JOIN apoderado AS apt ON apt.id_apoderado = matricula.id_ap_titular
                LEFT JOIN apoderado AS aps ON aps.id_apoderado = matricula.id_ap_suplente
                WHERE matricula.anio_lectivo = EXTRACT (YEAR FROM now())
                ORDER BY matricula.matricula DESC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $matriculas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($matriculas as $matricula) {
                // condición para el nombre social
                if ($matricula['n_social'] != null) {
                    $matricula['nombre'] = '('. $matricula['n_social']. ') '. $matricula['nombre']; 
                }
                $this->json['data'][] = $matricula;
                unset($this->json['data'][0]['n_social']); // Se elimina del array un dato innesesario
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener cantidad
        public function getCantidadMatricula() {
            $query = "SELECT SUM(CASE WHEN id_estado != 4 THEN 1 ELSE 0 END) AS cantidad_matricula,
                SUM(CASE WHEN id_estado = 4 THEN 1 ELSE 0 END) AS cantidad_retiro
                FROM matricula WHERE anio_lectivo = EXTRACT (YEAR FROM now());";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute()) {
                $resultado = $sentencia->fetch();

                $this->json['cantidad_matricula'] = $resultado['cantidad_matricula'];
                $this->json['cantidad_retiro'] = $resultado['cantidad_retiro'];
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para eliminar el registro de una matrícula
        public function deleteMatricula($id_matricula) {
            $query = "DELETE FROM matricula WHERE id_matricula = ?;";
            $sentencia = $this->preConsult($query);

            if ($sentencia->execute([$id_matricula])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para exportar el registro de las matriculas
        public function exportarMatriculas($ext) {
            $extension = 'Xlsx';
            $query = "SELECT matricula.matricula, curso.curso, (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante, estudiante.nombres_estudiante, estudiante.nombre_social,
                estudiante.fecha_nacimiento, estudiante.sexo, estudiante.fecha_ingreso, estudiante.fecha_retiro,
                (ap_titular.rut_apoderado || '-' || ap_titular.dv_rut_apoderado) AS rut_ap_titular,
                ap_titular.ap_apoderado AS ap_titular, ap_titular.am_apoderado AS am_titular, ap_titular.nombres_apoderado AS nombres_titular,
                ap_titular.telefono AS telefono_titular, ap_titular.direccion AS direccion_titular,
                (ap_suplente.rut_apoderado || '-' || ap_suplente.dv_rut_apoderado) AS rut_ap_suplente,
                ap_suplente.ap_apoderado AS ap_suplente, ap_suplente.am_apoderado AS am_suplente, ap_suplente.nombres_apoderado AS nombres_suplente,
                ap_suplente.telefono AS telefono_suplente, ap_suplente.direccion AS direccion_suplente, estado.nombre_estado
                FROM matricula
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                LEFT JOIN apoderado AS ap_titular ON ap_titular.id_apoderado = matricula.id_ap_titular
                LEFT JOIN apoderado AS ap_suplente ON ap_suplente.id_apoderado = matricula.id_ap_suplente
                LEFT JOIN estado ON estado.id_estado = matricula.id_estado
                WHERE matricula.anio_lectivo = EXTRACT (YEAR FROM now())
                ORDER BY estudiante.ap_estudiante ASC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();            
            $estudiantes = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro matriculas');
            
            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Matriculas");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:X3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:X3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'REGISTRO MATRICULA ESTUDIANTES PERIODO '. date('Y'));
            
            $sheetActive->getColumnDimension('A')->setWidth(10);
            $sheetActive->getColumnDimension('B')->setWidth(8);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(18);
            $sheetActive->getColumnDimension('E')->setWidth(18);
            $sheetActive->getColumnDimension('F')->setWidth(25);
            $sheetActive->getColumnDimension('G')->setWidth(15);
            $sheetActive->getColumnDimension('H')->setWidth(15);
            $sheetActive->getColumnDimension('I')->setWidth(8);
            $sheetActive->getColumnDimension('J')->setWidth(15);
            $sheetActive->getColumnDimension('K')->setWidth(15);  
            $sheetActive->getColumnDimension('L')->setWidth(15);  
            $sheetActive->getColumnDimension('M')->setWidth(18);  
            $sheetActive->getColumnDimension('N')->setWidth(18);  
            $sheetActive->getColumnDimension('O')->setWidth(25);  
            $sheetActive->getColumnDimension('P')->setWidth(15);  
            $sheetActive->getColumnDimension('Q')->setWidth(30);  
            $sheetActive->getColumnDimension('R')->setWidth(15);  
            $sheetActive->getColumnDimension('S')->setWidth(18);  
            $sheetActive->getColumnDimension('T')->setWidth(18);
            $sheetActive->getColumnDimension('U')->setWidth(25);  
            $sheetActive->getColumnDimension('V')->setWidth(15);  
            $sheetActive->getColumnDimension('W')->setWidth(30);
            $sheetActive->getColumnDimension('X')->setWidth(18);
            
            $sheetActive->getStyle('A:B')->getAlignment()->setHorizontal('center');
            $sheetActive->getStyle('H:K')->getAlignment()->setHorizontal('center');
            $sheetActive->getStyle('A3:X3')->getAlignment()->setHorizontal('left');

            $sheetActive->setCellValue('A3', 'MATRICULA');
            $sheetActive->setCellValue('B3', 'CURSO');
            $sheetActive->setCellValue('C3', 'RUT ESTUDIANTE');
            $sheetActive->setCellValue('D3', 'A_PATERNO ESTUDIANTE');
            $sheetActive->setCellValue('E3', 'A_MATERNO ESTUDIANTE');
            $sheetActive->setCellValue('F3', 'NOMBRES ESTUDIANTE');
            $sheetActive->setCellValue('G3', 'NOMBRE SOCIAL');
            $sheetActive->setCellValue('H3', 'FECHA NACIMIENTO');
            $sheetActive->setCellValue('I3', 'SEXO');
            $sheetActive->setCellValue('J3', 'FECHA INGRESO');
            $sheetActive->setCellValue('K3', 'FECHA RETIRO');
            $sheetActive->setCellValue('L3', 'RUT TITULAR');
            $sheetActive->setCellValue('M3', 'A_PATERNO TITULAR');
            $sheetActive->setCellValue('N3', 'A_MATERNO_TITULAR');
            $sheetActive->setCellValue('O3', 'NOMBRE TITULAR');
            $sheetActive->setCellValue('P3', 'TELÉFONO TITULAR');
            $sheetActive->setCellValue('Q3', 'DIRECCIÓN TITULAR');
            $sheetActive->setCellValue('R3', 'RUT SUPLENTE');
            $sheetActive->setCellValue('S3', 'A_PATERNO SUPLENTE');
            $sheetActive->setCellValue('T3', 'A_MATERNO SUPLENTE');
            $sheetActive->setCellValue('U3', 'NOMBRES SUPLENTE');
            $sheetActive->setCellValue('V3', 'TELÉFONO SUPLENTE');
            $sheetActive->setCellValue('W3', 'DIRECCIÓN SUPLENTE');
            $sheetActive->setCellValue('X3', 'ESTADO MATRÍCULA');

            $fila = 4;
            foreach ($estudiantes as $estudiante) {
                $sheetActive->setCellValue('A'.$fila, $estudiante['matricula']);
                $sheetActive->setCellValue('B'.$fila, $estudiante['curso']);
                $sheetActive->setCellValue('C'.$fila, $estudiante['rut_estudiante']);
                $sheetActive->setCellValue('D'.$fila, $estudiante['ap_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $estudiante['am_estudiante']);
                $sheetActive->setCellValue('F'.$fila, $estudiante['nombres_estudiante']);
                $sheetActive->setCellValue('G'.$fila, $estudiante['nombre_social']);
                $sheetActive->setCellValue('H'.$fila, $estudiante['fecha_nacimiento']);
                $sheetActive->setCellValue('I'.$fila, $estudiante['sexo']);
                $sheetActive->setCellValue('J'.$fila, $estudiante['fecha_ingreso']);
                $sheetActive->setCellValue('K'.$fila, $estudiante['fecha_retiro']);
                $sheetActive->setCellValue('L'.$fila, $estudiante['rut_ap_titular']);
                $sheetActive->setCellValue('M'.$fila, $estudiante['ap_titular']);
                $sheetActive->setCellValue('N'.$fila, $estudiante['am_titular']);
                $sheetActive->setCellValue('O'.$fila, $estudiante['nombres_titular']);
                $sheetActive->setCellValue('P'.$fila, $estudiante['telefono_titular']);
                $sheetActive->setCellValue('Q'.$fila, $estudiante['direccion_titular']);
                $sheetActive->setCellValue('R'.$fila, $estudiante['rut_ap_suplente']);
                $sheetActive->setCellValue('S'.$fila, $estudiante['ap_suplente']);
                $sheetActive->setCellValue('T'.$fila, $estudiante['am_suplente']);
                $sheetActive->setCellValue('U'.$fila, $estudiante['nombres_suplente']);
                $sheetActive->setCellValue('V'.$fila, $estudiante['telefono_suplente']);
                $sheetActive->setCellValue('W'.$fila, $estudiante['direccion_suplente']);
                $sheetActive->setCellValue('X'.$fila, $estudiante['nombre_estado']);
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
