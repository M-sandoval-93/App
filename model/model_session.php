<?php

    // SE INCLUYE EL ARCHIVO DE CONEXION BBDD
    require_once "../model/model_conexion.php";

    class Session extends Conexion {
        public function __construct() {
            parent:: __construct();
            // funcionality in the duration of the session
            // session_set_cookie_params(60*60) // Para considerar 1 hora de sesión activa
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

        // Método para verificar cuenta de usuario
        public function checkUsser($cuentaUsuario) {
            $md5Pass = md5($cuentaUsuario->clave); 
            
            $query = "SELECT (funcionario.nombres_funcionario || ' ' || funcionario.ap_funcionario || ' ' || funcionario.am_funcionario) AS nombre_usuario, 
                usuario.id_privilegio, usuario.id_usuario, usuario.fecha_ingreso, usuario.id_estado
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
                $this->json['status'] = $usuario['id_estado'];
                $this->json['id_usuario'] = $usuario['id_usuario'];
                $this->json['fecha_ingreso'] = $usuario['fecha_ingreso'];
                $this->res = true;
            }

            if ($this->json['fecha_ingreso'] != null) {
                $query = "UPDATE usuario SET fecha_ingreso = CURRENT_TIMESTAMP WHERE id_usuario = ?;";
                $sentencia = $this->preConsult($query);
                $sentencia->execute([intval($this->getId())]);
            }

            $this->json['data'] = $this->res;
            $this->closeConnection(); 
            return json_encode($this->json);
        }

        // Método para nueva password
        public function newPassword($password) {
            if ($password->password1 == $password->password2) {
                $md5Pass = md5($password->password1);
                $query = "UPDATE usuario SET clave_usuario = ?, fecha_ingreso = CURRENT_TIMESTAMP WHERE id_usuario = ?;";

                $sentencia = $this->preConsult($query);
                if ($sentencia->execute([$md5Pass, intval($password->id_usuario)])) {
                    $this->res = true;
                }
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Método para cerrar conexión con la base de datos
        public function closeSession() {
            $this->closeConnection();
            session_unset();
            session_destroy();
        }

    }


?>
