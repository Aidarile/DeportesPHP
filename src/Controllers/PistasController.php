<?php

class PistasController {

    public function __construct(private PistasGateway $gatewayPistas) {

    }

    public function processRequestPistas(string $method, ?string $id) {
        if ($id != null) {
            //Procesar petición de recurso
            $this -> processResourceRequest($method, $id);
        } else {
            //Procesar petición a la colección
            $this -> processCollectionRequest($method);
        }
    }

private function processResourceRequest(string $method, string $id) {
    $pista = $this -> gatewayPistas -> getPista($id);
        if (!$pista) {
            http_response_code(404);
            echo json_encode(["message" => "Pista con id {$id} no encontrada"]);
            return;
        }
        switch ($method) {
            case "GET":
                echo json_encode($pista);
                break;
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                $errors = $this -> getValidationErrors($data, false);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $rows = $this -> gatewayPistas -> updatePista ($pista, $data);

                echo json_encode([
                    "message" => "Pista con id {$id} actualizado",
                    "rows" => $rows 
                ]);
                break;
            case "DELETE":
                $rows = $this -> gatewayPistas -> deletePista($id);
                echo json_encode([
                    "message" => "Pista con id {$id} eliminado",
                    "rows" => "Han sido eliminadas {$rows} filas"]);
                break;
            default:
                http_response_code(405);
                header("Allow: GET, POST");
                break;
    }
}

private function processCollectionRequest(string $method) {
    switch ($method) {
        case "GET":
            echo json_encode($this -> gatewayPistas -> getAllPista());
            break;
            
        case "POST":
            $data = (array) json_decode (file_get_contents("php://input",true));

            $errors = $this -> getValidationErrors($data);
            if (!empty ($errors)) {
                http_response_code(422);    //unprocesable entity
                echo json_encode($errors);
                break; 
            }

            $id = $this -> gatewayPistas -> createPista ($data);
            http_response_code(201);  // "201" significa "OK/objeto creado"
            echo json_encode([
                "message" => "Pista creado",
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

        if ($is_new && (isset($data["nombre"]) || empty($data["nombre"]))) {
            $errors[]= "El nombre es obligatorio";  
        }
        if (array_key_exists("max_jugadores", $data)) {
            if (filter_var($data["max_jugadores"], FILTER_VALIDATE_INT) === false) {
                $errors[] = "El campo 'max_jugadores' debe ser un Entero";
            }
        }
        return $errors;
    }

}
?>