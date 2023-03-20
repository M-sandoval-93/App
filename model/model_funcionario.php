<?php

    require_once "../model/model_conexion.php";                         // Uso del modelo de conexión
    // require_once "../Pluggins/PhpOffice/vendor/autoload.php";           // Uso de la librería PHPSpreadsheet
    // require_once "../Pluggins/openTBS/tbs_class.php";                   // Uso de librería para trabajar con word
    // require_once "../Pluggins/openTBS/tbs_plugin_opentbs.php";          // Uso de librería para trabajar con word


    class Funcionario extends Conexion {

        public function __construct() {
            parent::__construct();
        }


        // Método para obtener la cantidad de funcionarios
        public function getCatidadFuncionario() {
            $query = "SELECT COUNT(id_funcionario) AS cantidad_funcionario FROM funcionario";

            $sentencia = $this->preConsult($query);
            $sentencia->execute();
            $cantidad = $sentencia->fetch();
            $this->json['cantidad_funcionario'] = $cantidad['cantidad_funcionario'];

            $this->closeConnection();
            return json_encode($this->json);
        }





    }



?>