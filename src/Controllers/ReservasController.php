<?php

class ReservasController {



    public function __construct(private ReservasGateway $gatewayReservas) {
    }

    public function processRequestReservas(string $method, ?string $id) {
        if ($id != null) {
            //Procesar petición de recurso
            $this -> processResourceRequest($method, $id);
        } else {
            //Procesar petición a la colección
            $this -> processCollectionRequest($method);
        }
    }

private function processResourceRequest(string $method, string $id): void {
    $reserva = $this -> gatewayReservas -> getReserva($id);
    if (!$reserva) {
        http_response_code(404);
        echo json_encode(["message" => "Reserva con id {$id} no encontrada"]);
        return;
    }
    switch ($method) {
        case "GET":
            echo json_encode($reserva);
            break;
        case "PATCH":
            $data = (array) json_decode(file_get_contents("php://input"), true);
            $errors = $this -> getValidationErrors($data, false);
            if (!empty($errors)) {
                http_response_code(422);
                echo json_encode(["errors" => $errors]);
                break;
            }
            $rows = $this -> gatewayReservas -> updateReserva ($reserva, $data);

            echo json_encode([
                "message" => "Reserva con id {$id} actualizado",
                "rows" => $rows 
            ]);
            break;
        case "DELETE":
            $rows = $this -> gatewayReservas -> deleteReserva($id);
            echo json_encode([
                "message" => "Reserva con id {$id} eliminada",
                "rows" => "Han sido eliminadas {$rows} filas"]);
            break;
        default:
            http_response_code(405);
            header("Allow: GET, POST");
            break;
    }
}

private function processCollectionRequest (string $method) {
    switch ($method) {
        case "GET":
            echo json_encode($this -> gatewayReservas -> getAllReserva());
            break;
            
        case "POST":
            $data = (array) json_decode (file_get_contents("php://input",true));

            $errors = $this -> getValidationErrors($data);
            if (!empty ($errors)) {
                http_response_code(422);    //unprocesable entity
                echo json_encode($errors);
                break; 
            }

            $id = $this -> gatewayReservas -> createReserva ($data);
            http_response_code(201);  // "201" significa "OK/objeto creado"
            echo json_encode([
                "message" => "Reserva creada",
                "id" => $id
            ]);
            break;

        default:
            http_response_code(405);
            header("Allow: GET, POST"); // para indicar que sólo están permitidos esos dos metodos
            break;
    }

}

private function getValidationErrors(array $data, bool $is_new = true) : array
 {
    $errors = [];

    if ($is_new && (!isset($data["socio"]) || empty($data["socio"]))) {
        $errors[]= "El id de la pista es obligatorio";  
    }
    if ($is_new && (!isset($data["pista"]) || empty($data["pista"]))) {
        $errors[]= "El id de la pista es obligatorio";  
    }
    
    return $errors;
}
    
}


?>