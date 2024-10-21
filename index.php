<?php

//1. Configurar el servidor para una entrada unica:

declare(strict_types= 1);

//6b. Controllar excepciones
require("src/ErrorHandler.php");
set_exception_handler("ErrorHandler::handleException");

require("src/Database.php");

require("src/Gateways/PistasGateway.php");
require("src/Gateways/ReservasGateway.php");
require("src/Gateways/SociosGateway.php");

require("src/Controllers/PistasController.php");
require("src/Controllers/ReservasController.php");
require("src/Controllers/SociosController.php");

//5c. Crear el modelo y la conexion a la BBDD
$database = new Database("localhost", "deportes_db", "root", "");
$gatewayPistas = new PistasGateway($database);
$gatewayReservas = new ReservasGateway($database);
$gatewaySocios = new SociosGateway($database);

$controllerPistas = new PistasController($gatewayPistas);
$controllerReservas = new ReservasController($gatewayReservas);
$controllerSocios = new SociosController($gatewaySocios);



Header("Content-type: application/json; charset=UTF-8");

$parts = explode("/", $_SERVER["REQUEST_URI"]);

$endpoint = $parts[1];
$id = $parts[2] ?? null;
$method = $_SERVER["REQUEST_METHOD"];

//2. Comprobar endpoints validos:

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
}
?>