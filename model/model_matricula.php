<?php

    // Uso del modelo de conexión
    include_once "../model/model_conexion.php";

    // Uso de la librería PHPSpreadsheet
    // require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    // use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};


    class MatriculaEstudiantes extends Conexion {

        public function __construct() {
            parent::__construct(); 
        }

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
                ORDER BY matricula.matricula DESC";

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

        public function deleteMatricula($id_matricula) { // trabajando.......
            $query = "DELETE FROM matricula WHERE id_matricula = ?;";
            $sentencia = $this->preConsult($query);

            if ($sentencia->execute([$id_matricula])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }



    }




?>