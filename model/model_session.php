<?php

    // SE INCLUYE EL ARCHIVO DE CONEXION BBDD
    require_once "../model/model_conexion.php";

    class Session extends Conexion {
        public function __construct() {
            parent:: __construct();
            session_start();
        }

        public function getUsser() {
            return $_SESSION['usser']['name'];
        }

        public function setUsser($name) {
            $_SESSION['usser']['name'] = $name;
        }

        public function getPrivilege() {
            return $_SESSION['usser']['privilege'];
        }

        public function setPrivilege($privilege) {
            $_SESSION['usser']['privilege'] = $privilege;
        }

        public function getId() {
            return $_SESSION['usser']['id'];
        }

        public function setId($id) {
            $_SESSION['usser']['id'] = $id;
        }

        public function checkUsser($usser, $pass) {
            // VARIABLES
            $md5Pass = md5($pass); 
            $query = "SELECT nombre_usuario, id_privilegio FROM usuario WHERE nombre_usuario = ? AND clave_usuario = ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([$usser, $md5Pass]);

            if ($usuario = $sentencia->fetch()) {
                $this->setUsser($usuario['nombre_usuario']);
                $this->setId($usuario['id_privilegio']);
                $this->res = true;
                $this->json['id'] = $this->getId();
            }

            $this->json['data'] = $this->res;

            return json_encode($this->json);
            $this->closeConnection(); 
        }

        public function closeSession() {
            $this->closeConnection();
            session_unset();
            session_destroy();
        }

    }


?>
