<?php

    // Uso del modelo de conexión
    require_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet 
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    use PhpOffice\PhpSpreadsheet\Style\{Color, Font};

    class Apoderado extends Conexion {

        public function __construct() {
            parent:: __construct();
        }

        // Método para obtener apoderados registrados
        public function getApoderados() {
            $query = "SELECT DISTINCT ON (apoderado.id_apoderado) id_apoderado, 
                (apoderado.rut_apoderado || '-' || apoderado.dv_rut_apoderado) AS rut_apoderado,
                apoderado.ap_apoderado, apoderado.am_apoderado, apoderado.nombres_apoderado,
                apoderado.telefono, apoderado.direccion,
                CASE WHEN mt.id_ap_titular IS NULL OR ms.id_ap_suplente IS NULL THEN 'NO ASIGNADO' ELSE 'ASIGNADO' END AS asignacion
                FROM apoderado
                LEFT JOIN matricula AS mt ON mt.id_ap_titular = apoderado.id_apoderado
                LEFT JOIN matricula AS ms ON ms.id_ap_suplente = apoderado.id_apoderado;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $apoderados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($apoderados as $apoderado) {
                $this->json['data'][] = $apoderado;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Metodo para obtener y comprobar datos de un apoderado
        public function getApoderado($rut, $tipo) {
            if ($tipo == 'comprobar') {
                $query = "SELECT id_apoderado FROM apoderado WHERE rut_apoderado = ?;";
                $sentencia = $this->preConsult($query);
                $sentencia->execute([$rut]);

                if ($sentencia->fetchColumn() > 0) {
                    $this->res = true;
                }

                $this->closeConnection();
                return json_encode($this->res);
            }

            if ($tipo == 'matricula') {
                $query = "SELECT id_apoderado,
                    (nombres_apoderado || ' ' || ap_apoderado || ' ' || am_apoderado) AS nombre_apoderado
                    FROM apoderado
                    WHERE rut_apoderado = ?";
                $sentencia = $this->preConsult($query);
                $sentencia->execute([$rut]);
                $apoderado = $sentencia->fetchAll(PDO::FETCH_ASSOC);

                foreach ($apoderado as $ap) {
                    $this->json[] = $ap;
                }

                $this->closeConnection();
                return json_encode($this->json);
            }


            // obtener los datos de una solo apoderado para algun evento de completar o asignar apoderado



        }

        // Método para obtener la cantidad de apoderados registrados
        public function getCantidadApoderado() {
            $query = "SELECT COUNT(id_apoderado) AS cantidad FROM apoderado;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $resultado = $sentencia->fetch();

            if ($resultado['cantidad'] >= 1) {
                $this->res = $resultado['cantidad'];
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para agregar un nuevo apoderado
        public function setApoderado($a) {
            $query = "SELECT id_apoderado FROM apoderado WHERE rut_apoderado = ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$a->rut]); 

            if ($sentencia->fetchColumn() > 0) {
                $this->closeConnection();
                return json_encode('existe');
            }

            $query = "INSERT INTO apoderado (rut_apoderado, dv_rut_apoderado, ap_apoderado, am_apoderado,
                nombres_apoderado, telefono, direccion) VALUES (?, ?, ?, ?, ?, ?, ?);";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$a->rut, $a->dv_rut, $a->ap, $a->am, $a->nombres, $a->telefono, $a->direccion])) {
                $this->res = true;
            }
            
            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para actualizar un apoderado
        public function updateApoderado($a) {
            $query = "UPDATE apoderado
                SET rut_apoderado = ?, dv_rut_apoderado = ?, ap_apoderado = ?, am_apoderado = ?, 
                nombres_apoderado = ?, telefono = ?, direccion = ? 
                WHERE id_apoderado = ?;";
            $sentencia = $this->preConsult($query);

            if ($sentencia->execute([$a->rut, $a->dv_rut, $a->ap, $a->am, $a->nombres, $a->telefono, $a->direccion, intval($a->id_apoderado)])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para eliminar el registro de un apoderado
        public function deleteApoderado($id_apoderado) {
            $query = "SELECT SUM(matricula.total) AS total
            FROM
            (SELECT COUNT(id_ap_titular) AS total FROM matricula WHERE id_ap_titular = ?
            UNION ALL
            SELECT COUNT(id_ap_suplente) AS total FROM matricula WHERE id_ap_suplente = ?) matricula;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$id_apoderado, $id_apoderado]);
            $resultado = $sentencia->fetch();

            if (!$resultado['total'] >= 1) {
                $query = "DELETE FROM apoderado WHERE id_apoderado = ?;";
                $sentencia = $this->preConsult($query);
                $sentencia->execute([$id_apoderado]);
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para exportar el registro de los estudiantes
        public function exportarApoderados($ext) {
            $extension = 'Xlsx';
            $query = "SELECT * FROM 
                (SELECT DISTINCT ON ((apoderado.rut_apoderado || '-' || apoderado.dv_rut_apoderado)) 
                (apoderado.rut_apoderado || '-' || apoderado.dv_rut_apoderado) AS rut_apoderado,
                apoderado.ap_apoderado, apoderado.am_apoderado, apoderado.nombres_apoderado,
                apoderado.telefono, apoderado.direccion,
                CASE WHEN mt.id_ap_titular IS NULL OR ms.id_ap_suplente IS NULL THEN 'NO ASIGNADO' ELSE 'ASIGNADO' END AS asignacion
                FROM apoderado
                LEFT JOIN matricula AS mt ON mt.id_ap_titular = apoderado.id_apoderado
                LEFT JOIN matricula AS ms ON ms.id_ap_suplente = apoderado.id_apoderado
                ORDER BY rut_apoderado) apoderados
                ORDER BY apoderados.ap_apoderado ASC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();            
            $apoderados = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro apoderados');
            
            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Apoderados");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:G3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:G3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'REGISTRO APODERADOS');
            
            $sheetActive->getColumnDimension('A')->setWidth(15);
            $sheetActive->getColumnDimension('B')->setWidth(18);
            $sheetActive->getColumnDimension('C')->setWidth(18);
            $sheetActive->getColumnDimension('D')->setWidth(32);
            $sheetActive->getColumnDimension('E')->setWidth(18);
            $sheetActive->getColumnDimension('F')->setWidth(40);
            $sheetActive->getColumnDimension('G')->setWidth(18);   
            // $sheetActive->getStyle('F:G')->getAlignment()->setHorizontal('center');  //centrado de lineas

            $sheetActive->setCellValue('A3', 'RUT');
            $sheetActive->setCellValue('B3', 'AP PATERNO');
            $sheetActive->setCellValue('C3', 'AP MATERNO');
            $sheetActive->setCellValue('D3', 'NOMBRES');
            $sheetActive->setCellValue('E3', 'TELÉFONO');
            $sheetActive->setCellValue('F3', 'DIRECCIÓN');
            $sheetActive->setCellValue('G3', 'ASIGNACIÓN');

            $fila = 4;
            foreach ($apoderados as $apoderado) {
                $sheetActive->setCellValue('A'.$fila, $apoderado['rut_apoderado']);
                $sheetActive->setCellValue('B'.$fila, $apoderado['ap_apoderado']);
                $sheetActive->setCellValue('C'.$fila, $apoderado['am_apoderado']);
                $sheetActive->setCellValue('D'.$fila, $apoderado['nombres_apoderado']);
                $sheetActive->setCellValue('E'.$fila, $apoderado['telefono']);
                $sheetActive->setCellValue('F'.$fila, $apoderado['direccion']);
                $sheetActive->setCellValue('G'.$fila, $apoderado['asignacion']);

                if ($apoderado['asignacion'] == 'NO ASIGNADO') {
                    $sheetActive->getStyle('A'.$fila. ':G'.$fila)->getFont()->getColor()->setARGB(Color::COLOR_RED);
                }

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
