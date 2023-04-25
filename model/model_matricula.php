<?php

    require_once "../model/model_conexion.php";                         // Uso del modelo de conexión
    require_once "../Pluggins/PhpOffice/vendor/autoload.php";           // Uso de la librería PHPSpreadsheet
    require_once "../Pluggins/openTBS/tbs_class.php";                   // Uso de librería para trabajar con word
    require_once "../Pluggins/openTBS/tbs_plugin_opentbs.php";          // Uso de librería para trabajar con word


    use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    use PhpOffice\PhpSpreadsheet\Style\{Color, Font};


    class MatriculaEstudiantes extends Conexion {

        public function __construct() {
            parent::__construct(); 
        }

        // Método para obtener datos de las matrículas
        public function getMatricula() {
            // $query = "SELECT matricula.id_matricula, matricula.matricula,
            //     (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
            //     estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
            //     (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
            //     '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombre,
            //     to_char(estudiante.fecha_nacimiento, 'DD / MM / YYYY') AS fecha_nacimiento,
            //     to_char(estudiante.fecha_ingreso, 'DD / MM / YYYY') AS fecha_ingreso,
            //     to_char(estudiante.fecha_retiro, 'DD / MM / YYYY') AS fecha_retiro,
            //     to_char(matricula.fecha_matricula, 'DD / MM / YYYY') AS fecha_matricula,
            //     CASE WHEN estudiante.sexo = 'M' THEN 'Masculimo' ELSE 'Femenina' END AS sexo,
            //     estado.nombre_estado, curso.curso, matricula.numero_lista,
            //     ('(' || apt.rut_apoderado || '-' || apt.dv_rut_apoderado || ') ' || '/ ' || apt.nombres_apoderado || ' ' || apt.ap_apoderado || ' ' || apt.am_apoderado
            //     || ' / Celular: +569-' || apt.telefono) AS apoderado_titular,
            //     ('(' || aps.rut_apoderado || '-' || aps.dv_rut_apoderado || ') ' || '/ ' || aps.nombres_apoderado || ' ' || aps.ap_apoderado || ' ' || aps.am_apoderado
            //     || ' / Celular: +569-' || aps.telefono) AS apoderado_suplente
            //     FROM matricula
            //     INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
            //     LEFT JOIN curso ON curso.id_curso = matricula.id_curso
            //     LEFT JOIN estado ON estado.id_estado = matricula.id_estado
            //     LEFT JOIN apoderado AS apt ON apt.id_apoderado = matricula.id_ap_titular
            //     LEFT JOIN apoderado AS aps ON aps.id_apoderado = matricula.id_ap_suplente
            //     WHERE matricula.anio_lectivo = EXTRACT (YEAR FROM now())
            //     ORDER BY matricula.matricula DESC;";

            $query = "SELECT matricula.id_matricula, matricula.matricula,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut,
                estudiante.ap_estudiante AS ap_paterno, estudiante.am_estudiante AS ap_materno,
                (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombre,
                COALESCE(to_char(estudiante.fecha_nacimiento, 'DD / MM / YYYY'), 'Sin registro') AS fecha_nacimiento,
                to_char(estudiante.fecha_ingreso, 'DD / MM / YYYY') AS fecha_ingreso,
                COALESCE(to_char(estudiante.fecha_retiro, 'DD / MM / YYYY'), 'Estudiante matriculado') AS fecha_retiro,
                to_char(matricula.fecha_matricula, 'DD / MM / YYYY') AS fecha_matricula,
                CASE WHEN estudiante.sexo = 'M' THEN 'Masculimo' ELSE 'Femenina' END AS sexo,
                estado.nombre_estado, curso.curso, matricula.numero_lista,
                (apt.rut_apoderado || '-' || apt.dv_rut_apoderado || '/ ' || apt.nombres_apoderado || ' ' || apt.ap_apoderado
                || ' ' || apt.am_apoderado) AS apoderado_titular, COALESCE('+569-' || apt.telefono, 'Sin registros') AS telefono_titular,
                (aps.rut_apoderado || '-' || aps.dv_rut_apoderado || '/ ' || aps.nombres_apoderado || ' ' || aps.ap_apoderado
                || ' ' || aps.am_apoderado) AS apoderado_suplente, COALESCE('+569-' || aps.telefono, 'Sin registros') AS telefono_suplente
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                LEFT JOIN curso ON curso.id_curso = matricula.id_curso
                LEFT JOIN estado ON estado.id_estado = matricula.id_estado
                LEFT JOIN apoderado AS apt ON apt.id_apoderado = matricula.id_ap_titular
                LEFT JOIN apoderado AS aps ON aps.id_apoderado = matricula.id_ap_suplente
                WHERE matricula.anio_lectivo = EXTRACT (YEAR FROM now())
                ORDER BY curso.curso ASC, matricula.numero_lista ASC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $matriculas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach ($matriculas as $matricula) {
                $this->json['data'][] = $matricula;
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

        // Método para obtener el número de lista correlativo por letra del curso
        public function getNumeroLista($id_curso) {
            $query = "SELECT (MAX(matricula.numero_lista) + 1) AS numero_lista
                FROM matricula
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE curso.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                AND matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                AND curso.id_curso = ?
                GROUP BY curso.curso;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$id_curso]);
            $numero_lista = $sentencia->fetch();

            $this->closeConnection();
            return json_encode($numero_lista['numero_lista']);
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

            $matricula = ($m->matricula == '0' or $m->matricula == '') ? null : intval($m->matricula);
            $n_lista = ($m->n_lista == '0' or $m->n_lista == '') ? null : intval($m->n_lista);
            $titular = ($m->id_titular == '0') ? null : intval($m->id_titular);
            $suplente = ($m->id_suplente == '0') ? null : intval($m->id_suplente);



            // Sentencia para el registro de una matricula
            $query = "INSERT INTO matricula (matricula, id_estudiante, id_ap_titular, id_ap_suplente, id_curso, anio_lectivo, fecha_matricula, numero_lista)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?);";
            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$matricula, $estudiante['id_estudiante'], $titular, $suplente, 
                intval($m->id_curso), intval(date('Y')), $m->fecha_matricula, $n_lista])) {
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
            $n_matricula = ($m->matricula == '0' or $m->matricula == '') ? null : intval($m->matricula);
            $n_lista = ($m->n_lista == '0' or $m->n_lista == '') ? null : intval($m->n_lista);
            $titular = ($m->id_titular == '0') ? null : intval($m->id_titular);
            $suplente = ($m->id_suplente == '0') ? null : intval($m->id_suplente);


            // Consulta para log cambio de curso y apoderado
            $query_curso = "SELECT id_estudiante, id_curso, id_ap_titular, id_ap_suplente, numero_lista
                FROM matricula WHERE id_matricula = ?;";

            $sentencia = $this->preConsult($query_curso);
            $sentencia->execute([intval($m->id_matricula)]);
            $matricula = $sentencia->fetch(PDO::FETCH_ASSOC);

            // -> Condición para registrar el histórico de los cambios de curso
            if ($matricula['id_curso'] != intval($m->id_curso)) {
                $query_log_cambio_curso = "INSERT INTO log_cambio_curso (fecha_cambio, id_estudiante, id_curso_actual, id_curso_nuevo, periodo, id_usuario, fecha_registro, 
                    old_num_lista, new_num_lista) VALUES (?, ?, ?, ?, EXTRACT(YEAR FROM CURRENT_DATE), ?, CURRENT_TIMESTAMP, ?, ?);";

                $sentencia = $this->preConsult($query_log_cambio_curso);
                $sentencia->execute([$m->fecha_cambio_curso, intval($matricula['id_estudiante']), intval($matricula['id_curso']), intval($m->id_curso), intval($m->id_usuario),
                                    intval($matricula['numero_lista']), $n_lista]);
            }

            // -> Condición para registrar cambio de apoderado titula
            if ($matricula['id_ap_titular'] != $titular) {
                $query_log_cambio_apoderado_titular = "INSERT INTO log_cambio_apoderado (fecha_cambio, id_estudiante, id_old_apoderado, id_new_apoderado,
                    tipo_apoderado, periodo, id_usuario) VALUES (CURRENT_TIMESTAMP, ?, ?, ?, 'TITULAR', EXTRACT(YEAR FROM CURRENT_DATE), ?);";

                $sentencia = $this->preConsult($query_log_cambio_apoderado_titular);
                $sentencia->execute([intval($matricula['id_estudiante']), ($matricula['id_ap_titular'] == 0) ? null : intval($matricula['id_ap_titular']), $titular, $m->id_usuario]);
            }

            // -> Condición para registrar cambio de apoderado suplente
            if ($matricula['id_ap_suplente'] != $suplente) {
                $query_log_cambio_apoderado_suplente = "INSERT INTO log_cambio_apoderado (fecha_cambio, id_estudiante, id_old_apoderado, id_new_apoderado,
                    tipo_apoderado, periodo, id_usuario) VALUES (CURRENT_TIMESTAMP, ?, ?, ?, 'SUPLENTE', EXTRACT(YEAR FROM CURRENT_DATE), ?);";

                $sentencia = $this->preConsult($query_log_cambio_apoderado_suplente);
                $sentencia->execute([intval($matricula['id_estudiante']), ($matricula['id_ap_suplente'] == 0) ? null : intval($matricula['id_ap_suplente']), $suplente, $m->id_usuario]);
            }

            // Actualización de la matrícula
            $query = "UPDATE matricula
                SET matricula = ?, id_ap_titular = ?, id_ap_suplente = ?, id_curso = ?, fecha_matricula = ?, numero_lista = ?
                WHERE id_matricula = ?;";
            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$n_matricula, $titular, $suplente, intval($m->id_curso), $m->fecha_matricula, $n_lista, intval($m->id_matricula)])) {
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

        // Método para generar certificado de matrícula
        public function getCertificado($id_matricula) {
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

        // Método para descargat altas de matrícula
        public function getAltaMatricula($fechas) {
            $f_inicio = ($fechas->f_inicio == '') ? date('Y').'-01-01' : $fechas->f_inicio;
            $f_termino = ($fechas->f_termino == '') ? date('Y').'-12-31' : $fechas->f_termino;

            $query = "SELECT to_char(matricula.fecha_matricula, 'DD/MM/YYYY') AS fecha_matricula,
                CASE WHEN matricula.id_curso IS NULL THEN 'N/A' ELSE curso.curso END AS curso,
                CASE WHEN matricula.matricula IS NULL THEN 'N/A' ELSE CAST(matricula.matricula AS VARCHAR) END AS matricula,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante,
                (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombres_estudiante,
                CASE WHEN matricula.id_estado = 1 THEN 'Matriculado(a)'
                WHEN matricula.id_estado = 4 THEN 'Retirado(a)'
                WHEN matricula.id_estado = 5 THEN 'Suspendido(a)' END AS estado_matricula
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE matricula.fecha_matricula >= ?
                AND matricula.fecha_matricula <= ?;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$f_inicio, $f_termino]);
            $altas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro altas matrícula');
            
            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Altas de matrículas");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:H3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:H3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'ALTAS DE MATRÍCULA PERIODO '. date('Y'));
             
            $sheetActive->getColumnDimension('A')->setWidth(20);
            $sheetActive->getColumnDimension('B')->setWidth(12);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(18);
            $sheetActive->getColumnDimension('E')->setWidth(20);
            $sheetActive->getColumnDimension('F')->setWidth(20);
            $sheetActive->getColumnDimension('G')->setWidth(30);
            $sheetActive->getColumnDimension('H')->setWidth(18);
            $sheetActive->getStyle('B:C')->getAlignment()->setHorizontal('center');
 
            $sheetActive->setCellValue('A3', 'FECHA MATRÍCULA');
            $sheetActive->setCellValue('B3', 'CURSO');
            $sheetActive->setCellValue('C3', 'MATRÍCULA');
            $sheetActive->setCellValue('D3', 'RUT');
            $sheetActive->setCellValue('E3', 'PATERNO');
            $sheetActive->setCellValue('F3', 'MATERNO');
            $sheetActive->setCellValue('G3', 'NOMBRES');
            $sheetActive->setCellValue('H3', 'ESTADO');
 
            $fila = 4;
            foreach ($altas as $alta) {
                $sheetActive->setCellValue('A'.$fila, $alta['fecha_matricula']);
                $sheetActive->setCellValue('B'.$fila, $alta['curso']);
                $sheetActive->setCellValue('C'.$fila, $alta['matricula']);
                $sheetActive->setCellValue('D'.$fila, $alta['rut_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $alta['ap_estudiante']);
                $sheetActive->setCellValue('F'.$fila, $alta['am_estudiante']);
                $sheetActive->setCellValue('G'.$fila, $alta['nombres_estudiante']);
                $sheetActive->setCellValue('H'.$fila, $alta['estado_matricula']);
                $fila++;
            }
            
            $writer = IOFactory::createWriter($file, 'Xlsx');

            ob_start();
            $writer->save('php://output');
            $documentData = ob_get_contents();
            ob_end_clean();

            $file = array ( "data" => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; base64,'.base64_encode($documentData));

            $this->closeConnection();
            return json_encode($file);
        }

        // Método para descargar reporte de cambio de curso
        public function getCambioCurso($fechas) {
            $f_inicio = ($fechas->f_inicio == '') ? date('Y').'-01-01' : $fechas->f_inicio;
            $f_termino = ($fechas->f_termino == '') ? date('Y').'-12-31' : $fechas->f_termino;

            $query = "SELECT to_char(log_cambio_curso.fecha_cambio, 'DD/MM/YYYY') AS fecha_cambio,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante,
                (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombres_estudiante,
                cursoActual.curso AS curso_antiguo, cursoNuevo.curso AS curso_nuevo
                FROM log_cambio_curso
                INNER JOIN estudiante ON estudiante.id_estudiante = log_cambio_curso.id_estudiante
                INNER JOIN curso AS cursoActual ON cursoActual.id_curso = log_cambio_curso.id_curso_actual
                INNER JOIN curso AS cursoNuevo ON cursoNuevo.id_curso = log_cambio_curso.id_curso_nuevo
                WHERE log_cambio_curso.fecha_cambio >= ? 
                AND log_cambio_curso.fecha_cambio <= ?;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$f_inicio, $f_termino]);
            $cambiosCurso = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro cambios curso');
            
            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Cambios de curso");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:G3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:G3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'CAMBIOS DE CURSO ESTUDIANTES PERIODO '. date('Y'));
             
            $sheetActive->getColumnDimension('A')->setWidth(20);
            $sheetActive->getColumnDimension('B')->setWidth(20);
            $sheetActive->getColumnDimension('C')->setWidth(20);
            $sheetActive->getColumnDimension('D')->setWidth(20);
            $sheetActive->getColumnDimension('E')->setWidth(30);
            $sheetActive->getColumnDimension('F')->setWidth(20);
            $sheetActive->getColumnDimension('G')->setWidth(20);
            $sheetActive->getStyle('F:G')->getAlignment()->setHorizontal('center');
 
            $sheetActive->setCellValue('A3', 'FECHA CAMBIO');
            $sheetActive->setCellValue('B3', 'RUT');
            $sheetActive->setCellValue('C3', 'PATERNO');
            $sheetActive->setCellValue('D3', 'MATERNO');
            $sheetActive->setCellValue('E3', 'NOMBRES');
            $sheetActive->setCellValue('F3', 'CURSO ANTIGUO');
            $sheetActive->setCellValue('G3', 'CURSO NUEVO');
 
            $fila = 4;
            foreach ($cambiosCurso as $cambioCurso) {
                $sheetActive->setCellValue('A'.$fila, $cambioCurso['fecha_cambio']);
                $sheetActive->setCellValue('B'.$fila, $cambioCurso['rut_estudiante']);
                $sheetActive->setCellValue('C'.$fila, $cambioCurso['ap_estudiante']);
                $sheetActive->setCellValue('D'.$fila, $cambioCurso['am_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $cambioCurso['nombres_estudiante']);
                $sheetActive->setCellValue('F'.$fila, $cambioCurso['curso_antiguo']);
                $sheetActive->setCellValue('G'.$fila, $cambioCurso['curso_nuevo']);
                $fila++;
            }
            
            $writer = IOFactory::createWriter($file, 'Xlsx');

            ob_start();
            $writer->save('php://output');
            $documentData = ob_get_contents();
            ob_end_clean();

            $file = array ( "data" => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; base64,'.base64_encode($documentData));

            $this->closeConnection();
            return json_encode($file);
        }

        // Método para descargar reporte de retiros de matrícula
        public function getRetiroMatricula($fechas) {
            $f_inicio = ($fechas->f_inicio == '') ? date('Y').'-01-01' : $fechas->f_inicio;
            $f_termino = ($fechas->f_termino == '') ? date('Y').'-12-31' : $fechas->f_termino;

            $query = "SELECT to_char(estudiante.fecha_retiro, 'DD/MM/YYYY') AS fecha_retiro,
                CASE WHEN matricula.id_curso IS NULL THEN 'N/A' ELSE curso.curso END AS curso,
                CASE WHEN matricula.matricula IS NULL THEN 'N/A' ELSE CAST(matricula.matricula AS VARCHAR) END AS matricula,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante,
                (CASE WHEN estudiante.nombre_social IS NULL THEN estudiante.nombres_estudiante ELSE
                '(' || estudiante.nombre_social || ') ' || estudiante.nombres_estudiante END) AS nombres_estudiante
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_matricula
                INNER JOIN curso ON curso.id_curso = matricula.id_curso
                WHERE matricula.id_estado = 4 AND estudiante.fecha_retiro >= ?
                AND estudiante.fecha_retiro <= ?;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$f_inicio, $f_termino]);
            $retiros = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro retiros matrícula');
            
            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Retiros de matrículas");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:G3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:G3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'RETIROS DE MATRÍCULA PERIODO '. date('Y'));
             
            $sheetActive->getColumnDimension('A')->setWidth(18);
            $sheetActive->getColumnDimension('B')->setWidth(12);
            $sheetActive->getColumnDimension('C')->setWidth(15);
            $sheetActive->getColumnDimension('D')->setWidth(18);
            $sheetActive->getColumnDimension('E')->setWidth(20);
            $sheetActive->getColumnDimension('F')->setWidth(20);
            $sheetActive->getColumnDimension('G')->setWidth(30);
            $sheetActive->getStyle('B:C')->getAlignment()->setHorizontal('center');
 
            $sheetActive->setCellValue('A3', 'FECHA RETIRO');
            $sheetActive->setCellValue('B3', 'CURSO');
            $sheetActive->setCellValue('C3', 'MATRÍCULA');
            $sheetActive->setCellValue('D3', 'RUT');
            $sheetActive->setCellValue('E3', 'PATERNO');
            $sheetActive->setCellValue('F3', 'MATERNO');
            $sheetActive->setCellValue('G3', 'NOMBRES');
 
            $fila = 4;
            foreach ($retiros as $retiro) {
                $sheetActive->setCellValue('A'.$fila, $retiro['fecha_retiro']);
                $sheetActive->setCellValue('B'.$fila, $retiro['curso']);
                $sheetActive->setCellValue('C'.$fila, $retiro['matricula']);
                $sheetActive->setCellValue('D'.$fila, $retiro['rut_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $retiro['ap_estudiante']);
                $sheetActive->setCellValue('F'.$fila, $retiro['am_estudiante']);
                $sheetActive->setCellValue('G'.$fila, $retiro['nombres_estudiante']);
                $fila++;
            }
            
            $writer = IOFactory::createWriter($file, 'Xlsx');

            ob_start();
            $writer->save('php://output');
            $documentData = ob_get_contents();
            ob_end_clean();

            $file = array ( "data" => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; base64,'.base64_encode($documentData));

            $this->closeConnection();
            return json_encode($file);
        }

        // Método para descargar reporte de matrícula
        public function getReporteMatricula($fechas) {
            $f_inicio = ($fechas->f_inicio == '') ? '2022-01-01' : $fechas->f_inicio;
            $f_termino = ($fechas->f_termino == '') ? date('Y').'-12-31' : $fechas->f_termino;

            $query = "SELECT CASE WHEN matricula.matricula IS NULL THEN 'N/A' ELSE CAST(matricula.matricula AS VARCHAR) END AS matricula,
                COALESCE(curso.curso, 'N/A') AS curso,
                CASE WHEN matricula.numero_lista IS NULL THEN 'N/A' ELSE CAST(matricula.numero_lista AS VARCHAR) END AS numero_lista,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante, estudiante.nombres_estudiante, estudiante.nombre_social,
                estudiante.fecha_nacimiento, estudiante.sexo, matricula.fecha_matricula, estudiante.fecha_retiro,
                (ap_titular.rut_apoderado || '-' || ap_titular.dv_rut_apoderado) AS rut_ap_titular,
                ap_titular.ap_apoderado AS ap_titular, ap_titular.am_apoderado AS am_titular, ap_titular.nombres_apoderado AS nombres_titular,
                ap_titular.telefono AS telefono_titular, ap_titular.direccion AS direccion_titular,
                (ap_suplente.rut_apoderado || '-' || ap_suplente.dv_rut_apoderado) AS rut_ap_suplente,
                ap_suplente.ap_apoderado AS ap_suplente, ap_suplente.am_apoderado AS am_suplente, ap_suplente.nombres_apoderado AS nombres_suplente,
                ap_suplente.telefono AS telefono_suplente, ap_suplente.direccion AS direccion_suplente, 
                CASE WHEN matricula.id_estado = 1 THEN 'Matriculado(a)'
                WHEN matricula.id_estado = 4 THEN 'Retirado(a)'
                WHEN matricula.id_estado = 5 THEN 'Suspendido(a)' END AS estado_matricula
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                LEFT JOIN curso ON curso.id_curso = matricula.id_curso
                LEFT JOIN apoderado AS ap_titular ON ap_titular.id_apoderado = matricula.id_ap_titular
                LEFT JOIN apoderado AS ap_suplente ON ap_suplente.id_apoderado = matricula.id_ap_suplente
                LEFT JOIN estado ON estado.id_estado = matricula.id_estado
                WHERE matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                AND matricula.fecha_matricula >= ? AND matricula.fecha_matricula <= ?
                ORDER BY estudiante.ap_estudiante ASC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$f_inicio, $f_termino]);
            $matriculas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro matrícula');

            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Retiros de matrículas");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:Y3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:Y3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'Registro de matrículas periodo '. date('Y'));
            
            $sheetActive->getColumnDimension('A')->setWidth(18);
            $sheetActive->getColumnDimension('B')->setWidth(12);
            $sheetActive->getColumnDimension('C')->setWidth(13);
            $sheetActive->getColumnDimension('D')->setWidth(15);
            $sheetActive->getColumnDimension('E')->setWidth(20);
            $sheetActive->getColumnDimension('F')->setWidth(20);
            $sheetActive->getColumnDimension('G')->setWidth(30);
            $sheetActive->getColumnDimension('H')->setWidth(20);
            $sheetActive->getColumnDimension('I')->setWidth(20);
            $sheetActive->getColumnDimension('J')->setWidth(10);
            $sheetActive->getColumnDimension('K')->setWidth(20);
            $sheetActive->getColumnDimension('L')->setWidth(20);
            $sheetActive->getColumnDimension('M')->setWidth(15);
            $sheetActive->getColumnDimension('N')->setWidth(20);
            $sheetActive->getColumnDimension('O')->setWidth(20);
            $sheetActive->getColumnDimension('P')->setWidth(30);
            $sheetActive->getColumnDimension('Q')->setWidth(12);
            $sheetActive->getColumnDimension('R')->setWidth(90);
            $sheetActive->getColumnDimension('S')->setWidth(15);
            $sheetActive->getColumnDimension('T')->setWidth(20);
            $sheetActive->getColumnDimension('U')->setWidth(20);
            $sheetActive->getColumnDimension('V')->setWidth(30);
            $sheetActive->getColumnDimension('W')->setWidth(12);
            $sheetActive->getColumnDimension('X')->setWidth(90);
            $sheetActive->getColumnDimension('Y')->setWidth(25);
            $sheetActive->getStyle('A:C')->getAlignment()->setHorizontal('center');
            $sheetActive->getStyle('J')->getAlignment()->setHorizontal('center');
            $sheetActive->getStyle('A1')->getAlignment()->setHorizontal('left');

            $sheetActive->setCellValue('A3', 'N° MATRÍCULA');
            $sheetActive->setCellValue('B3', 'CURSO');
            $sheetActive->setCellValue('C3', 'N° LISTA');
            $sheetActive->setCellValue('D3', 'RUT');
            $sheetActive->setCellValue('E3', 'PATERNO');
            $sheetActive->setCellValue('F3', 'MATERNO');
            $sheetActive->setCellValue('G3', 'NOMBRES');
            $sheetActive->setCellValue('H3', 'NOMBRE SOCIAL');
            $sheetActive->setCellValue('I3', 'FECHA NACIMIENTO');
            $sheetActive->setCellValue('J3', 'SEXO');
            $sheetActive->setCellValue('K3', 'FECHA MATRÍCULA');
            $sheetActive->setCellValue('L3', 'FECHA RETIRO');
            $sheetActive->setCellValue('M3', 'RUT TITULAR');
            $sheetActive->setCellValue('N3', 'PATERNO TITULAR');
            $sheetActive->setCellValue('O3', 'MATERNO TITULAR');
            $sheetActive->setCellValue('P3', 'NOMBRES TITULAR');
            $sheetActive->setCellValue('Q3', 'TELEFONO TITULAR');
            $sheetActive->setCellValue('R3', 'DIRECCIÓN TITULAR');
            $sheetActive->setCellValue('S3', 'RUT SUPLENTE');
            $sheetActive->setCellValue('T3', 'PATERNO SUPLENTE');
            $sheetActive->setCellValue('U3', 'MATERNO SUPLENTE');
            $sheetActive->setCellValue('V3', 'NOMBRES SUPLENTE');
            $sheetActive->setCellValue('W3', 'TELEFONO SUPLENTE');
            $sheetActive->setCellValue('X3', 'DIRECCIÓN SUPLENTE');
            $sheetActive->setCellValue('Y3', 'ESTADO MATRÍCULA');

            $fila = 4;
            foreach ($matriculas as $matricula) {
                $sheetActive->setCellValue('A'.$fila, $matricula['matricula']);
                $sheetActive->setCellValue('B'.$fila, $matricula['curso']);
                $sheetActive->setCellValue('C'.$fila, $matricula['numero_lista']);
                $sheetActive->setCellValue('D'.$fila, $matricula['rut_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $matricula['ap_estudiante']);
                $sheetActive->setCellValue('F'.$fila, $matricula['am_estudiante']);
                $sheetActive->setCellValue('G'.$fila, $matricula['nombres_estudiante']);
                $sheetActive->setCellValue('H'.$fila, $matricula['nombre_social']);
                $sheetActive->setCellValue('I'.$fila, $matricula['fecha_nacimiento']);
                $sheetActive->setCellValue('J'.$fila, $matricula['sexo']);
                $sheetActive->setCellValue('K'.$fila, $matricula['fecha_matricula']);
                $sheetActive->setCellValue('L'.$fila, $matricula['fecha_retiro']);
                $sheetActive->setCellValue('M'.$fila, $matricula['rut_ap_titular']);
                $sheetActive->setCellValue('N'.$fila, $matricula['ap_titular']);
                $sheetActive->setCellValue('O'.$fila, $matricula['am_titular']);
                $sheetActive->setCellValue('P'.$fila, $matricula['nombres_titular']);
                $sheetActive->setCellValue('Q'.$fila, $matricula['telefono_titular']);
                $sheetActive->setCellValue('R'.$fila, $matricula['direccion_titular']);
                $sheetActive->setCellValue('S'.$fila, $matricula['rut_ap_suplente']);
                $sheetActive->setCellValue('T'.$fila, $matricula['ap_suplente']);
                $sheetActive->setCellValue('U'.$fila, $matricula['am_suplente']);
                $sheetActive->setCellValue('V'.$fila, $matricula['nombres_suplente']);
                $sheetActive->setCellValue('W'.$fila, $matricula['telefono_suplente']);
                $sheetActive->setCellValue('X'.$fila, $matricula['direccion_suplente']);
                $sheetActive->setCellValue('Y'.$fila, $matricula['estado_matricula']);
                $fila++;
            }

            $writer = IOFactory::createWriter($file, 'Xlsx');

            ob_start();
            $writer->save('php://output');
            $documentData = ob_get_contents();
            ob_end_clean();

            $file = array ( "data" => 'data:application/vnd.openxmlformats-officedocument.spreadsheetml.sheet; base64,'.base64_encode($documentData));

            $this->closeConnection();
            return json_encode($file);
        }

        // Método para exportar el registro de las matriculas
        public function exportarMatriculas($ext) {
            $extension = 'Xlsx';
            $query = "SELECT CASE WHEN matricula.matricula IS NULL THEN 'N/A' ELSE CAST(matricula.matricula AS VARCHAR) END AS matricula,
                COALESCE(curso.curso, 'N/A') AS curso,
                CASE WHEN matricula.numero_lista IS NULL THEN 'N/A' ELSE CAST(matricula.numero_lista AS VARCHAR) END AS numero_lista,
                (estudiante.rut_estudiante || '-' || estudiante.dv_rut_estudiante) AS rut_estudiante,
                estudiante.ap_estudiante, estudiante.am_estudiante, estudiante.nombres_estudiante, estudiante.nombre_social,
                estudiante.fecha_nacimiento, estudiante.sexo, matricula.fecha_matricula, estudiante.fecha_retiro,
                (ap_titular.rut_apoderado || '-' || ap_titular.dv_rut_apoderado) AS rut_ap_titular,
                ap_titular.ap_apoderado AS ap_titular, ap_titular.am_apoderado AS am_titular, ap_titular.nombres_apoderado AS nombres_titular,
                ap_titular.telefono AS telefono_titular, ap_titular.direccion AS direccion_titular,
                (ap_suplente.rut_apoderado || '-' || ap_suplente.dv_rut_apoderado) AS rut_ap_suplente,
                ap_suplente.ap_apoderado AS ap_suplente, ap_suplente.am_apoderado AS am_suplente, ap_suplente.nombres_apoderado AS nombres_suplente,
                ap_suplente.telefono AS telefono_suplente, ap_suplente.direccion AS direccion_suplente, 
                CASE WHEN matricula.id_estado = 1 THEN 'Matriculado(a)'
                WHEN matricula.id_estado = 4 THEN 'Retirado(a)'
                WHEN matricula.id_estado = 5 THEN 'Suspendido(a)' END AS estado_matricula
                FROM matricula
                INNER JOIN estudiante ON estudiante.id_estudiante = matricula.id_estudiante
                LEFT JOIN curso ON curso.id_curso = matricula.id_curso
                LEFT JOIN apoderado AS ap_titular ON ap_titular.id_apoderado = matricula.id_ap_titular
                LEFT JOIN apoderado AS ap_suplente ON ap_suplente.id_apoderado = matricula.id_ap_suplente
                LEFT JOIN estado ON estado.id_estado = matricula.id_estado
                WHERE matricula.anio_lectivo = EXTRACT(YEAR FROM CURRENT_DATE)
                ORDER BY estudiante.ap_estudiante ASC;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();            
            $matriculas = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            // Preparar archivo
            $file = new Spreadsheet();
            $file
                ->getProperties()
                ->setCreator("Dpto. Informática")
                ->setLastModifiedBy('Informática')
                ->setTitle('Registro matrícula');

            $file->setActiveSheetIndex(0);
            $sheetActive = $file->getActiveSheet();
            $sheetActive->setTitle("Retiros de matrículas");
            $sheetActive->setShowGridLines(false);
            $sheetActive->getStyle('A1')->getFont()->setBold(true)->setSize(18);
            $sheetActive->getStyle('A3:Y3')->getFont()->setBold(true)->setSize(12);
            $sheetActive->setAutoFilter('A3:Y3');

            $sheetActive->mergeCells('A1:D1');
            $sheetActive->setCellValue('A1', 'Registro de matrículas periodo '. date('Y'));
            
            $sheetActive->getColumnDimension('A')->setWidth(18);
            $sheetActive->getColumnDimension('B')->setWidth(12);
            $sheetActive->getColumnDimension('C')->setWidth(13);
            $sheetActive->getColumnDimension('D')->setWidth(15);
            $sheetActive->getColumnDimension('E')->setWidth(20);
            $sheetActive->getColumnDimension('F')->setWidth(20);
            $sheetActive->getColumnDimension('G')->setWidth(30);
            $sheetActive->getColumnDimension('H')->setWidth(20);
            $sheetActive->getColumnDimension('I')->setWidth(20);
            $sheetActive->getColumnDimension('J')->setWidth(10);
            $sheetActive->getColumnDimension('K')->setWidth(20);
            $sheetActive->getColumnDimension('L')->setWidth(20);
            $sheetActive->getColumnDimension('M')->setWidth(15);
            $sheetActive->getColumnDimension('N')->setWidth(20);
            $sheetActive->getColumnDimension('O')->setWidth(20);
            $sheetActive->getColumnDimension('P')->setWidth(30);
            $sheetActive->getColumnDimension('Q')->setWidth(12);
            $sheetActive->getColumnDimension('R')->setWidth(90);
            $sheetActive->getColumnDimension('S')->setWidth(15);
            $sheetActive->getColumnDimension('T')->setWidth(20);
            $sheetActive->getColumnDimension('U')->setWidth(20);
            $sheetActive->getColumnDimension('V')->setWidth(30);
            $sheetActive->getColumnDimension('W')->setWidth(12);
            $sheetActive->getColumnDimension('X')->setWidth(90);
            $sheetActive->getColumnDimension('Y')->setWidth(25);
            $sheetActive->getStyle('A:C')->getAlignment()->setHorizontal('center');
            $sheetActive->getStyle('J')->getAlignment()->setHorizontal('center');
            $sheetActive->getStyle('A1')->getAlignment()->setHorizontal('left');

            $sheetActive->setCellValue('A3', 'N° MATRÍCULA');
            $sheetActive->setCellValue('B3', 'CURSO');
            $sheetActive->setCellValue('C3', 'N° LISTA');
            $sheetActive->setCellValue('D3', 'RUT');
            $sheetActive->setCellValue('E3', 'PATERNO');
            $sheetActive->setCellValue('F3', 'MATERNO');
            $sheetActive->setCellValue('G3', 'NOMBRES');
            $sheetActive->setCellValue('H3', 'NOMBRE SOCIAL');
            $sheetActive->setCellValue('I3', 'FECHA NACIMIENTO');
            $sheetActive->setCellValue('J3', 'SEXO');
            $sheetActive->setCellValue('K3', 'FECHA MATRÍCULA');
            $sheetActive->setCellValue('L3', 'FECHA RETIRO');
            $sheetActive->setCellValue('M3', 'RUT TITULAR');
            $sheetActive->setCellValue('N3', 'PATERNO TITULAR');
            $sheetActive->setCellValue('O3', 'MATERNO TITULAR');
            $sheetActive->setCellValue('P3', 'NOMBRES TITULAR');
            $sheetActive->setCellValue('Q3', 'TELEFONO TITULAR');
            $sheetActive->setCellValue('R3', 'DIRECCIÓN TITULAR');
            $sheetActive->setCellValue('S3', 'RUT SUPLENTE');
            $sheetActive->setCellValue('T3', 'PATERNO SUPLENTE');
            $sheetActive->setCellValue('U3', 'MATERNO SUPLENTE');
            $sheetActive->setCellValue('V3', 'NOMBRES SUPLENTE');
            $sheetActive->setCellValue('W3', 'TELEFONO SUPLENTE');
            $sheetActive->setCellValue('X3', 'DIRECCIÓN SUPLENTE');
            $sheetActive->setCellValue('Y3', 'ESTADO MATRÍCULA');

            $fila = 4;
            foreach ($matriculas as $matricula) {
                $sheetActive->setCellValue('A'.$fila, $matricula['matricula']);
                $sheetActive->setCellValue('B'.$fila, $matricula['curso']);
                $sheetActive->setCellValue('C'.$fila, $matricula['numero_lista']);
                $sheetActive->setCellValue('D'.$fila, $matricula['rut_estudiante']);
                $sheetActive->setCellValue('E'.$fila, $matricula['ap_estudiante']);
                $sheetActive->setCellValue('F'.$fila, $matricula['am_estudiante']);
                $sheetActive->setCellValue('G'.$fila, $matricula['nombres_estudiante']);
                $sheetActive->setCellValue('H'.$fila, $matricula['nombre_social']);
                $sheetActive->setCellValue('I'.$fila, $matricula['fecha_nacimiento']);
                $sheetActive->setCellValue('J'.$fila, $matricula['sexo']);
                $sheetActive->setCellValue('K'.$fila, $matricula['fecha_matricula']);
                $sheetActive->setCellValue('L'.$fila, $matricula['fecha_retiro']);
                $sheetActive->setCellValue('M'.$fila, $matricula['rut_ap_titular']);
                $sheetActive->setCellValue('N'.$fila, $matricula['ap_titular']);
                $sheetActive->setCellValue('O'.$fila, $matricula['am_titular']);
                $sheetActive->setCellValue('P'.$fila, $matricula['nombres_titular']);
                $sheetActive->setCellValue('Q'.$fila, $matricula['telefono_titular']);
                $sheetActive->setCellValue('R'.$fila, $matricula['direccion_titular']);
                $sheetActive->setCellValue('S'.$fila, $matricula['rut_ap_suplente']);
                $sheetActive->setCellValue('T'.$fila, $matricula['ap_suplente']);
                $sheetActive->setCellValue('U'.$fila, $matricula['am_suplente']);
                $sheetActive->setCellValue('V'.$fila, $matricula['nombres_suplente']);
                $sheetActive->setCellValue('W'.$fila, $matricula['telefono_suplente']);
                $sheetActive->setCellValue('X'.$fila, $matricula['direccion_suplente']);
                $sheetActive->setCellValue('Y'.$fila, $matricula['estado_matricula']);
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
