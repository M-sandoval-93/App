<?php
    // Incluimos el modelo que utilizara el controlador
    require_once '../model/model_matricula.php';
    require_once "../model/model_session.php";

    $session = new Session();
    $id_usuario = $session->getId();

    $type = $_POST['datos']; // Recibimos la acción a realizar por el controlador
    $datosMatricula = new MatriculaEstudiantes(); // Creamos el objeto para trabajar con datatable

    switch ($type) {
        case "getMatricula":
            print $datosMatricula->getMatricula();
            break;

        case "getCantidadMatricula":
            print $datosMatricula->getCantidadMatricula();
            break;

        case "getNumeroMatricula":
            print $datosMatricula->getNumeroMatricula($_POST['inicial'], $_POST['final']);
            break;

        case "getApoderadoTS":
            print $datosMatricula->getApoderadoTS($_POST['id_matricula']);
            break;

        case "setMatricula":
            $matricula = json_decode(json_encode($_POST['matricula'])); // Convertir un objeto js a un objeto php
            print($datosMatricula->setMatricula($matricula));
            break;

        case "setSuspension":
            if ($id_usuario == 4) {
                http_response_code(404);
                exit();
            }

            $suspension = json_decode(json_encode($_POST['suspension'])); // Convertir un objeto js a un objeto php
            print($datosMatricula->setSuspension($suspension));
            break;

        case "setRetiroMatricula":
            if ($id_usuario == 4) {
                http_response_code(404);
                exit();
            }

            $retiro = json_decode(json_encode($_POST['retiro'])); // Convertir un objeto js a un objeto php
            print($datosMatricula->setRetiroMatricula($retiro));
            break;

        case "updateMatricula":
            if ($id_usuario == 4) {
                http_response_code(404);
                exit();
            }

            $matricula = json_decode(json_encode($_POST['matricula'])); // Convertir un objeto js a un objeto php
            print($datosMatricula->updateMatricula($matricula));
            break;
        
        case "deleteMatricula":
            if ($id_usuario == 4) {
                http_response_code(404);
                exit();
            }

            print $datosMatricula->deleteMatricula($_POST['id_matricula']);
            break;

        case "getCertificado":
            if ($id_usuario == 4) {
                http_response_code(404);
                exit();
            }

            print $datosMatricula->getCertificado($_POST['id_matricula']);
            break;

        case "exportarMatriculas":
            print $datosMatricula->exportarMatriculas($_POST['ext']);
            break;
    }



?>
