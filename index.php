<?php

declare(strict_types= 1);

require("src/ErrorHandler.php");
set_exception_handler("ErrorHandler::handleError");
set_error_handler("ErrorHandler::handleError");

require("src/Database.php");

require("src/Gateways/PistasGateway.php");
require("src/Gateways/ReservasGateway.php");
require("src/Gateways/SociosGateway.php");

require("src/Controllers/PistasController.php");
require("src/Controllers/ReservasController.php");
require("src/Controllers/SociosController.php");


$database = new Database("localhost", "deportes_db", "root", "");
$gatewayPistas = new PistasGateway($database);
$gatewayReservas = new ReservasGateway($database);
$gatewaySocios = new SociosGateway($database);

$controllerPistas = new PistasController($gatewayPistas);
$controllerReservas = new ReservasController($gatewayReservas);
$controllerSocios = new SociosController($gatewaySocios);



header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

$endpoint = $parts[2] ?? null;
$id = $parts[3] ?? null;
$method = $_SERVER["REQUEST_METHOD"];



switch ($endpoint) {
    case "socio":
        $controllerSocios -> processRequestSocios($method, $id);
    break;

    case "pista":
        $controllerPistas -> processRequestPistas($method, $id);
    break;
    
    case "reserva":
        $controllerReservas -> processRequestReservas($method, $id);
    break;

    default:
    http_response_code(404); // <- No encontrado
    echo json_encode(["Pagina no encontrada"]);
    break;
}
?>