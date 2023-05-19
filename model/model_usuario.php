<?php

    require_once "../model/model_conexion.php";                         // Uso del modelo de conexión
    
    // Uso de la librería PHPSpreadsheet
    // require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    // use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    // use PhpOffice\PhpSpreadsheet\Style\{Color, Font};


    class Usuario extends Conexion {

        public function __construct() {
            parent::__construct();
        }

        // Método para cargar lista de privilegios para usuarios
        public function loadPrivilegio() {
            $query = "SELECT * FROM privilegio;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $privilegios = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            $this->json[] = "<option value='0' selected> -------------- </option>";
            foreach($privilegios as $privilegio) {
                $this->json[] = "<option value='".$privilegio['id_privilegio']."' data-description='".$privilegio['descripcion']."' >".$privilegio['privilegio']."</option>";
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        public function checkUserAccount($id) {
            $query = "select id_funcionario from usuario where id_funcionario = ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($id)]);
            $resultado = $sentencia->fetchColumn();

            if ($resultado > 0) { $this->res = true; }

            $this->closeConnection();
            return json_encode($this->res);
        }

    }



?>