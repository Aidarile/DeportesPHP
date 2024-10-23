<?php

class ReservasController {

    public function __construct(private ReservasGateway $gatewayReservas) {
    }

    public function processRequestReservas(string $method, ?string $id) {
        if ($id != null) {
            $this->processResourceRequest($method, $id);
        } else {
            $this->processCollectionRequest($method);
        }
    }

    private function processResourceRequest(string $method, string $id): void {
        $reserva = $this->gatewayReservas->getReserva($id);
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
                $dataReserva = (array) json_decode(file_get_contents("php://input"), true);
                    $errors = $this->getValidationErrors($dataReserva, false);
                    if(!empty($errors)){
                        http_response_code(422);
                        echo json_encode(["errors"=>$errors]);
                        break;
                    }
                    $rows =$this->gatewayReservas->updateReserva($reserva, $dataReserva);
                    http_response_code(206);
                    echo json_encode(["message" => "Reserva con id: $id ha sido actualizado", "rows affected: "=>$rows]);
                    break;

            case "DELETE":
                $rows = $this->gatewayReservas->deleteReserva($id);
                echo json_encode([
                    "message" => "Reserva con id {$id} eliminada",
                    "rows" => $rows
                ]);
                break;
            default:
                http_response_code(405);
                header("Permitido: GET, PATCH, DELETE");
                break;
        }
    }

    private function processCollectionRequest(string $method) {
        switch ($method) {
            case "GET":
                echo json_encode($this->gatewayReservas->getAllReserva());
                break;
            
            case "POST":
                $dataReserva = (array) json_decode(file_get_contents("php://input",true));
                 $errors=$this->getValidationErrors($dataReserva);
                $id=$this->gatewayReservas->createReserva($dataReserva);


                if(!empty($errors)){
                    http_response_code(422); //unprocesable entity
                    echo json_encode($errors);
                    break;
                }
                 
                 if($id==0)
                 {
                    http_response_code(409); 
                    echo json_encode([
                        "Reserva no creada debido a que el socio está penalizado" 
                       ]);
                       break;
                 }

                 http_response_code(201);
                 echo json_encode([
                     "message" => "reserva creada", "id" => $id
                    ]);
                    break;
                   
                    default:
                        http_response_code(405);
                        header("Allow: GET, POST");
                        break;
        }
    }

    private function getValidationErrors(array $data, bool $is_new = true): array {
        $errors = [];

        if ($is_new && (!isset($data["socio"]) || empty($data["socio"]))) {
            $errors[] = "El id del socio es obligatorio";  
        }

        elseif ($is_new && (!isset($data["pista"]) || empty($data["pista"]))) {
            $errors[] = "El id de la pista es obligatorio";  
        } 

        if ($is_new && (!isset($data["fecha"]) || empty($data["fecha"]))) {
            $errors[] = "La fecha es obligatoria";
        }

        if ($is_new && (!isset($data["hora"]) || empty($data["hora"]))) {
            $errors[] = "La hora es obligatoria";
        }

        return $errors;
    }
}

?>
