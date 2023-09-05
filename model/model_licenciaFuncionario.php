<?php

    require_once "../model/model_conexion.php";                         // Uso del modelo de conexión
    
    // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    use PhpOffice\PhpSpreadsheet\Style\{Color, Font};


    class LicenciaFuncionario extends Conexion {

        public function __construct() {
            parent::__construct();
        }

        // Método para obtener datos de los funcionarios
        public function getLicencias() {
            $query = "SELECT licencia_funcionario.id_licencia, 
                funcionario.rut_funcionario
                FROM licencia_funcionario
                INNER JOIN funcionario ON funcionario.id_funcionario = licencia_funcionario.id_funcionario
                ORDER BY funcionario.ap_funcionario ASC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $funcionarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach($funcionarios as $funcionario) {
                $this->json['data'][] = $funcionario;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }


        // Método para obtener la cantidad de funcionarios
        // public function getCatidadFuncionario() {
        //     $query = "SELECT COUNT(id_funcionario) AS cantidad_funcionario FROM funcionario";

        //     $sentencia = $this->preConsult($query);
        //     $sentencia->execute();
        //     $cantidad = $sentencia->fetch();
        //     $this->json['cantidad_funcionario'] = $cantidad['cantidad_funcionario'];

        //     $this->closeConnection();
        //     return json_encode($this->json);
        // }


        // Método para agregar un nuevo funcionario
        // public function setFuncionario($f) {
        //     $query = "SELECT id_funcionario FROM funcionario WHERE rut_funcionario = ?;";
        //     $sentencia = $this->preConsult($query);
        //     $sentencia->execute([$f->rut]);

        //     if ($sentencia->fetchColumn() > 0) {
        //         $this->closeConnection();
        //         return json_encode('existe');
        //     }

        //     $query = "INSERT INTO funcionario (rut_funcionario, dv_rut_funcionario, ap_funcionario, am_funcionario, nombres_funcionario, 
        //         sexo, fecha_nacimiento, id_estado, id_tipo_funcionario, id_departamento)
        //         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?);";

        //     $sentencia = $this->preConsult($query);
        //     if ($sentencia->execute([$f->rut, $f->dv_rut, $f->ap, $f->am, $f->nombre, $f->sexo, $f->fecha_nacimiento, 1, intval($f->tipo_funcionario), intval($f->departamento)])) {
        //         $this->res = true;
        //     }

        //     $this->closeConnection();
        //     return json_encode($this->res);
        // }

        // Método para actualizar datos de un funcionario
        // public function updateFuncionario($f) {
        //     $query = "UPDATE funcionario
        //         SET rut_funcionario = ?, dv_rut_funcionario = ?, ap_funcionario = ?, am_funcionario = ?, nombres_funcionario = ?, sexo = ?,
        //         fecha_nacimiento = ?, id_tipo_funcionario = ?, id_departamento = ?
        //         WHERE id_funcionario = ?;";
        //     $sentencia = $this->preConsult($query);

        //     if ($sentencia->execute([$f->rut, $f->dv_rut, $f->ap, $f->am, $f->nombre, $f->sexo, 
        //     $f->fecha_nacimiento, intval($f->tipo_funcionario), intval($f->departamento), intval($f->id_funcionario)])) {
        //         $this->res = true;
        //     }

        //     $this->closeConnection();
        //     return json_encode($this->res);
        // }

        // Método para eliminar el registro de un funcionario
        // public function deleteFuncionario($id_funcionario) {
        //     $query = "DELETE FROM funcionario WHERE id_funcionario = ?;";
        //     $sentencia = $this->preConsult($query);

        //     try {
        //         if ($sentencia->execute([intval($id_funcionario)])) {
        //             $this->res = true;
        //         }
        //     } catch (\Throwable $th) {}


        //     $this->closeConnection();
        //     return json_encode($this->res);
        // }

        // Método para descargar reporte funcionarios
        // public function getReporteFuncionario($ext) {
        //     $extension = 'Xlsx';
        //     $query = "SELECT (funcionario.rut_funcionario || '-' || funcionario.dv_rut_funcionario) AS rut_funcionario,
        //         funcionario.ap_funcionario, funcionario.am_funcionario, funcionario.nombres_funcionario, 
        //         CASE WHEN funcionario.sexo = 'M' THEN 'Masculino' ELSE 'Femenino' END AS sexo,
        //         to_char(funcionario.fecha_nacimiento, 'DD/MM/YYYY') AS fecha_nacimiento, estado.nombre_estado AS estado,
        //         tipo_funcionario.tipo_funcionario, departamento.departamento
        //         FROM funcionario
        //         LEFT JOIN estado ON estado.id_estado = funcionario.id_estado
        //         LEFT JOIN tipo_funcionario ON tipo_funcionario.id_tipo_funcionario = funcionario.id_tipo_funcionario
        //         LEFT JOIN departamento ON departamento.id_departamento = funcionario.id_departamento
        //         ORDER BY funcionario.ap_funcionario ASC;";

        //     $sentencia = $this->preConsult($query);
        //     $sentencia->execute();            
        //     $funcionarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

        //     // Preparar archivo
        //     $file = new Spreadsheet();
        //     $file
        //         ->getProperties()
        //         ->setCreator("Dpto. Informática")
        //         ->setLastModifiedBy('Informática')
        //         ->setTitle('Registro funcionarios');
            
        //     $file->setActiveSheetIndex(0);
        //     $sheetActive = $file->getActiveSheet();
        //     $sheetActive->setTitle("Funcionarios");
        //     $sheetActive->setShowGridLines(false);
        //     $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
        //     $sheetActive->getStyle('A3:I3')->getFont()->setBold(true)->setSize(12);
        //     $sheetActive->setAutoFilter('A3:I3');

        //     $sheetActive->mergeCells('A1:D1');
        //     $sheetActive->setCellValue('A1', 'REGISTRO FUNCIONARIOS');
            
        //     $sheetActive->getColumnDimension('A')->setWidth(15);
        //     $sheetActive->getColumnDimension('B')->setWidth(18);
        //     $sheetActive->getColumnDimension('C')->setWidth(18);
        //     $sheetActive->getColumnDimension('D')->setWidth(30);
        //     $sheetActive->getColumnDimension('E')->setWidth(15);
        //     $sheetActive->getColumnDimension('F')->setWidth(15);
        //     $sheetActive->getColumnDimension('G')->setWidth(15);
        //     $sheetActive->getColumnDimension('H')->setWidth(30);
        //     $sheetActive->getColumnDimension('I')->setWidth(30);      

        //     $sheetActive->setCellValue('A3', 'RUT');
        //     $sheetActive->setCellValue('B3', 'AP PATERNO');
        //     $sheetActive->setCellValue('C3', 'AP MATERNO');
        //     $sheetActive->setCellValue('D3', 'NOMBRES');
        //     $sheetActive->setCellValue('E3', 'SEXO');
        //     $sheetActive->setCellValue('F3', 'FECHA NACIMIENTO');
        //     $sheetActive->setCellValue('G3', 'ESTADO');
        //     $sheetActive->setCellValue('H3', 'TIPO FUNCIONARIO');
        //     $sheetActive->setCellValue('I3', 'DEPARTAMENTO');

        //     $fila = 4;
        //     foreach ($funcionarios as $funcionario) {
        //         $sheetActive->setCellValue('A'.$fila, $funcionario['rut_funcionario']);
        //         $sheetActive->setCellValue('B'.$fila, $funcionario['ap_funcionario']);
        //         $sheetActive->setCellValue('C'.$fila, $funcionario['am_funcionario']);
        //         $sheetActive->setCellValue('D'.$fila, $funcionario['nombres_funcionario']);
        //         $sheetActive->setCellValue('E'.$fila, $funcionario['sexo']);
        //         $sheetActive->setCellValue('F'.$fila, $funcionario['fecha_nacimiento']);
        //         $sheetActive->setCellValue('G'.$fila, $funcionario['estado']);
        //         $sheetActive->setCellValue('H'.$fila, $funcionario['tipo_funcionario']);
        //         $sheetActive->setCellValue('I'.$fila, $funcionario['departamento']);
        //         $fila++;
        //     }

        //     if ($ext == 'csv') {
        //         $extension = 'Csv';
        //     } 
            
        //     $writer = IOFactory::createWriter($file, $extension);

        //     ob_start();
        //     $writer->save('php://output');
        //     $documentData = ob_get_contents();
        //     ob_end_clean();

        //     $file = array ( "data" => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; base64,'.base64_encode($documentData));

        //     $this->closeConnection();
        //     return json_encode($file);
        // }

    }



?>