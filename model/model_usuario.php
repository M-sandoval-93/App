<?php

    // require_once "../model/model_conexion.php";                         // Uso del modelo de conexión
    require_once "../model/model_session.php";                          // Trae incluido el modelo conexión

    
    // Uso de la librería PHPSpreadsheet
    // require_once "../Pluggins/PhpOffice/vendor/autoload.php";
    // use PhpOffice\PhpSpreadsheet\{Spreadsheet, IOFactory};
    // use PhpOffice\PhpSpreadsheet\Style\{Color, Font};


    class Usuario extends Conexion {

        public function __construct() {
            parent::__construct();
        }

        // Method to get the user accounts create
        public function getUserAccount() {
            $query = "SELECT usuario.id_usuario, usuario.nombre_usuario, (funcionario.nombres_funcionario || ' ' || funcionario.ap_funcionario 
                || '' || funcionario.am_funcionario) AS funcionario, departamento.departamento, privilegio.privilegio,
                CASE WHEN estado.id_estado = 1 THEN 'Cuenta activa' ELSE 'Cuenta suspendida' END AS estado,
                estado.id_estado AS bloqueo
                FROM usuario
                LEFT JOIN funcionario ON funcionario.id_funcionario = usuario.id_funcionario
                LEFT JOIN departamento ON departamento.id_departamento = funcionario.id_departamento
                LEFT JOIN privilegio ON privilegio.id_privilegio = usuario.id_privilegio
                LEFT JOIN estado ON estado.id_estado = usuario.id_estado
                WHERE id_usuario != ?
                ORDER BY funcionario.ap_funcionario ASC;";
            
            $session = new Session();
            $id_user = $session->getId();

            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($id_user)]);
            $users = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            foreach($users as $user) {
                $this->json['data'][] = $user;
            }

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Method to get number of user accounts created
        public function getUserAccountAmount() {
            $query = "SELECT COUNT(id_usuario) AS amount_user FROM usuario";

            $statement = $this->preConsult($query);
            $statement->execute();
            $amount = $statement->fetch();
            $this->json['amount_user'] = $amount['amount_user'];

            $this->closeConnection();
            return json_encode($this->json);
        }

        // Method to load list of prigileges for users
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

        // Method to check the existence of a user account
        public function checkUserAccount($id) {
            $query = "select id_funcionario from usuario where id_funcionario = ?;";
            $sentencia = $this->preConsult($query);
            $sentencia->execute([intval($id)]);
            $resultado = $sentencia->fetchColumn();

            if ($resultado > 0) { $this->res = true; }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Method to create a user account
        public function setUserAccount($userAccount) {
            $query = "INSERT INTO usuario (nombre_usuario, clave_usuario, id_funcionario, id_privilegio, fecha_creacion)
                VALUES (?, ?, ?, ?, CURRENT_TIMESTAMP);";

            $sentencia = $this->preConsult($query);
            if ($sentencia->execute([$userAccount->usuario, md5($userAccount->clave), intval($userAccount->id_funcionario), intval($userAccount->id_privilegio)])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        public function updatePrivilegeUserAccount($idAccount, $idPrivilege) {
            $query = "UPDATE usuario SET id_privilegio = ?
                WHERE id_usuario = ?;";

            $statement = $this->preConsult($query);
            if ($statement->execute([intval($idPrivilege), intval($idAccount)])) {
                $this->res = true;
            }
            
            $this->closeConnection();
            return json_encode($this->res);
        }

        // Method to modify user Account
        public function modifyUserAccount($id_account) {
            $query = "UPDATE usuario SET id_estado =  CASE
                WHEN id_estado = 1 THEN 2
                WHEN id_estado = 2 THEN 1 END
                WHERE id_usuario = ?;";

            $statement = $this->preConsult($query);
            if ($statement->execute([intval($id_account)])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Password reset method
        public function restKeyAccount($id_account, $key) {
            $query = "UPDATE usuario SET clave_usuario = ?, fecha_ingreso = ?
                WHERE id_usuario = ?;";

            $statement = $this->preConsult($query);
            if ($statement->execute([md5($key), null, intval($id_account)])) {
                $this->res = true;
            }

            $this->closeConnection();
            return json_encode($this->res);
        }

        // Method to delete user account
        public function deleteUserAccount($id_account) {
            $query = "DELETE FROM usuario WHERE id_usuario = ?;";
            $sentencia = $this->preConsult($query);

            try {
                if ($sentencia->execute([intval($id_account)])) {
                    $this->res = true;
                }
            } catch (PDOException $e) {
                if ($e->getCode() == '23503') {
                    // para controlar error de clave foranea u otros !!
                }
            }

            $this->closeConnection();
            return json_encode($this->res);
        }
    }



?>