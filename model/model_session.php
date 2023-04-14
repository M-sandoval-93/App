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

        public function checkUsser($cuentaUsuario) {
            $md5Pass = md5($cuentaUsuario->clave); 
            $query = "SELECT (funcionario.nombres_funcionario || ' ' || funcionario.ap_funcionario || ' ' || funcionario.am_funcionario) AS nombre_usuario, 
                usuario.id_privilegio, usuario.id_usuario, usuario.fecha_ingreso
                FROM usuario 
                INNER JOIN funcionario ON funcionario.id_funcionario = usuario.id_funcionario
                WHERE nombre_usuario = ? AND clave_usuario = ?;";

            $sentencia = $this->preConsult($query);
            $sentencia->execute([$cuentaUsuario->usuario, $md5Pass]);
            if ($usuario = $sentencia->fetch()) {
                if ($usuario['fecha_ingreso'] != null) {
                    $this->setUsser($usuario['nombre_usuario']);
                    $this->setPrivilege($usuario['id_privilegio']);
                    $this->setId($usuario['id_usuario']);
                    $this->json['privilege'] = $this->getPrivilege();
                }
                $this->json['id_usuario'] = $usuario['id_usuario'];
                $this->json['fecha_ingreso'] = $usuario['fecha_ingreso'];
                $this->res = true;
            }

            $this->json['data'] = $this->res;
            $this->closeConnection(); 
            return json_encode($this->json);
        }

        public function newPassword($password) {
            if ($password->password1 === $password->password2) {
                $md5Pass = md5($password->password1);
                $query = "UPDATE usuario SET clave_usuario = ? WHERE id_usuario = ?;";

                $sentencia = $this->preConsult($query);
                if ($sentencia->execute([$md5Pass, $password->id_usuario])) {
                    $this->res = true;
                }
            }

            $this->closeConnection();
            return json_encode($this->res);

        }

        // public function checkUsser($usser, $pass) {
        //     // VARIABLES
        //     $md5Pass = md5($pass); 
        //     $query = "SELECT (funcionario.nombres_funcionario || ' ' || funcionario.ap_funcionario || ' ' || funcionario.am_funcionario) AS nombre_usuario, 
        //         usuario.id_privilegio, usuario.id_usuario, usuario.fecha_ingreso
        //         FROM usuario 
        //         INNER JOIN funcionario ON funcionario.id_funcionario = usuario.id_funcionario
        //         WHERE nombre_usuario = ? AND clave_usuario = ?;";

        //     $sentencia = $this->preConsult($query);
        //     $sentencia->execute([$usser, $md5Pass]);

        //     if ($usuario = $sentencia->fetch()) {
        //         $this->setUsser($usuario['nombre_usuario']);
        //         $this->setPrivilege($usuario['id_privilegio']);
        //         $this->setId($usuario['id_usuario']);
        //         $this->res = true;
        //         $this->json['privilege'] = $this->getPrivilege();
        //         $this->json['fecha_ingreso'] = $usuario['fecha_ingreso'];
        //     }

        //     $this->json['data'] = $this->res;

        //     return json_encode($this->json);
        //     $this->closeConnection(); 
        // }

        public function closeSession() {
            $this->closeConnection();
            session_unset();
            session_destroy();
        }

    }


?>
