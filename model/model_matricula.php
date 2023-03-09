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
        public function getMatricula() {
            $query = "SELECT matricula.id_matricula, matricula.matricula,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                estudiante.nombres_estudiante AS nombre, estudiante.nombre_social AS n_social, curso.curso,
                to_char(estudiante.fecha_nacimiento, 'DD / MM / YYYY') AS fecha_nacimiento,
                to_char(estudiante.fecha_ingreso, 'DD / MM / YYYY') AS fecha_ingreso,
                to_char(matricula.fecha_matricula, 'DD / MM / YYYY') AS fecha_matricula,
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

        // Método para obtener el numero correlativo de la matricula según el grado
        public function getNumeroMatricula($inicial, $final) {
            $query = "SELECT MAX(matricula.matricula) + 1 AS matricula
                FROM matricula
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE substr(curso.curso, 1,1)::integer >= ? AND substr(curso.curso, 1,1)::integer <= ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($inicial), intval($final)]);
            $matricula = $sentencia->fetch();

            $this->closeConnection();
            return json_encode($matricula['matricula']);
        }

        // Método para obtener cantidad
        public function getCantidadMatricula() {
            $query = "SELECT COALESCE(SUM(CASE WHEN id_estado != 4 THEN 1 ELSE 0 END), 0) AS cantidad_matricula,
                COALESCE(SUM(CASE WHEN id_estado = 4 THEN 1 ELSE 0 END), 0) AS cantidad_retiro
                FROM matricula WHERE anio_lectivo = EXTRACT (YEAR FROM CURRENT_DATE);";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute()) {
                $resultado = $sentencia->fetch();

                $this->json['cantidad_matricula'] = $resultado['cantidad_matricula'];
                $this->json['cantidad_retiro'] = $resultado['cantidad_retiro'];
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Método para obtener los apoderados de un estudiante
        public function getApoderadoTS($id_matricula) {
            $query = "SELECT titular.rut_apoderado AS rut_titular, suplente.rut_apoderado AS rut_suplente
                FROM matricula
                LEFT JOIN apoderado AS titular ON titular.id_apoderado = matricula.id_ap_titular
                LEFT JOIN apoderado AS suplente ON suplente.id_apoderado = matricula.id_ap_suplente
                WHERE matricula.id_matricula = ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($id_matricula)]);
            $apoderados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            $this->closeConnection();
            return json_encode($apoderados);
        }

        // Método para registrar nueva matrícula
        public function setMatricula($m) {
            // Sentencia para verificar si el rut ya cuenta con una matricula registrada
            $query = "SELECT estudiante.id_estudiante
                FROM matricula
                LEFT JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                WHERE estudiante.rut_estudiante = ? AND matricula.anio_lectivo = EXTRACT (YEAR FROM now());";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$m->rut]);

            if ($sentencia->fetchColumn() > 0) {
                $this->closeConnection();
                return json_encode('existe');
            }



            // función para saber si el numero de matricula para el grado, ya se encuentra en uso
            $query = "SELECT matricula.matricula, substr(curso.curso, 1,1) AS grado
                FROM matricula
                LEFT JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE matricula.matricula = ? AND 
                CASE WHEN (? >= 7 AND ? <= 8) 
                THEN substr(curso.curso, 1,1)::integer  >= 7 AND substr(curso.curso, 1,1)::integer <= 8
                ELSE substr(curso.curso, 1,1)::integer  >= 1 AND substr(curso.curso, 1,1)::integer <= 4
                END;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($m->matricula), intval($m->grado), intval($m->grado)]);

            if ($sentencia->rowCount() > 0) {
                $this->closeConnection();
                return json_encode('matriculaExiste');
            }



            // Sentencia para obtener el id del estudiante
            $query = "SELECT id_estudiante FROM estudiante WHERE rut_estudiante = ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$m->rut]);
            $estudiante = $sentencia->fetch();

            $titular = ($m->id_titular == '0') ? null : intval($m->id_titular);
            $suplente = ($m->id_suplente == '0') ? null : intval($m->id_suplente);



            // Sentencia para el registro de una matricula
            $query = "INSERT INTO matricula (matricula, id_estudiante, id_ap_titular, id_ap_suplente, id_curso, anio_lectivo, fecha_matricula)
                VALUES (?, ?, ?, ?, ?, ?, ?);";
            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([intval($m->matricula), $estudiante['id_estudiante'], $titular, $suplente, 
                intval($m->id_curso), intval(date('Y')), $m->fecha_matricula])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        } 

        // Método para registrar la suspención de una matrícula
        public function setSuspension($s) {
            // insert suspención
            $query = "INSERT INTO suspension_estudiante (id_estudiante, fecha_inicio, fecha_termino, motivo)
                SELECT id_estudiante, ? AS fecha_inicio, ? AS fecha_termino, ? AS motivo FROM matricula where id_matricula = ?;"; 

            $motivo = ($s->motivo == '') ? null : $s->motivo;

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$s->f_inicio, $s->f_termino, $motivo, intval($s->id_matricula)])) {
                // Update estado
                $query = "UPDATE matricula
                    SET id_estado = 5
                    WHERE id_matricula = ?;";
                    
                $sentencia = $this->preConsult($query);
                if ($sentencia->execute([intval($s->id_matricula)])) {
                    $this->res = true;
                }
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para registrar el retiro de una matrícula
        public function setRetiroMatricula($retiro) {
            // update fecha retiro
            $query = "UPDATE estudiante
                SET fecha_retiro = ?
                WHERE rut_estudiante = ?;";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$retiro->fecha_retiro, $retiro->rut])) {
                // Update estado
                $query = "UPDATE matricula
                    SET id_estado = 4
                    WHERE id_matricula = ?;";
                    
                $sentencia = $this->preConsult($query);
                if ($sentencia->execute([intval($retiro->id_matricula)])) {
                    $this->res = true;
                }
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para actualizar una matrícula
        public function updateMatricula($m) {
            $titular = ($m->id_titular == '0') ? null : intval($m->id_titular);
            $suplente = ($m->id_suplente == '0') ? null : intval($m->id_suplente);

            $query = "UPDATE matricula
                SET matricula = ?, id_ap_titular = ?, id_ap_suplente = ?, id_curso = ?, fecha_matricula = ?
                WHERE id_matricula = ?;";
            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([intval($m->matricula), $titular, $suplente, intval($m->id_curso), $m->fecha_matricula, intval($m->id_matricula)])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para eliminar el registro de una matrícula
        public function deleteMatricula($id_matricula) {
            $query = "DELETE FROM matricula WHERE id_matricula = ?;";
            $sentencia = $this->preConsult($query);

            if ($sentencia->execute([intval($id_matricula)])) {
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
