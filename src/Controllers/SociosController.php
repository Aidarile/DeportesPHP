<?php

class SociosController {

    public function __construct(private SociosGateway $gatewaySocios) {

    }

    public function processRequestSocios(string $method, ?string $id): void {
        if ($id != null) {
            //Procesar petición de recurso
            $this -> processResourceRequest($method, $id);
        } else {
            //Procesar petición a la colección
            $this -> processCollectionRequest($method);
        }
    }

private function processResourceRequest(string $method, string $id): void {
    $socio = $this -> gatewaySocios -> getSocio($id);
        if (!$socio) {
            http_response_code(404);
            echo json_encode(["message" => "Socio con id {$id} no encontrado"]);
            return;
        }
        switch ($method) {
            case "GET":
                echo json_encode($socio);
                break;
            case "PATCH":
                $data = (array) json_decode(file_get_contents("php://input"), true);
                $errors = $this -> getValidationErrors($data, false);
                if (!empty($errors)) {
                    http_response_code(422);
                    echo json_encode(["errors" => $errors]);
                    break;
                }
                $rows = $this -> gatewaySocios -> updateSocio ($socio, $data);

                echo json_encode([
                    "message" => "Socio con id {$id} actualizado",
                    "rows" => $rows 
                ]);
                break;
            case "DELETE":
                $rows = $this -> gatewaySocios -> deleteSocio($id);
                echo json_encode([
                    "message" => "Socio con id {$id} eliminado",
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
            echo json_encode($this -> gatewaySocios -> getAllSocio());
            break;
            
        case "POST":
            $data = (array) json_decode (file_get_contents("php://input",true));

            $errors = $this -> getValidationErrors($data);
            if (!empty ($errors)) {
                http_response_code(422);    //unprocesable entity
                echo json_encode($errors);
                break; 
            }

            $id = $this -> gatewaySocios -> createSocio ($data);
            http_response_code(201);  // "201" significa "OK/objeto creado"
            echo json_encode([
                "message" => "Socio creado",
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
        if($is_new && (!isset($data["nombre"]) || empty($data["nombre"])) ){
            $errors[] = "El nombre es obligatorio";
        }
        if($is_new && (!isset($data["telefono"]) || empty($data["telefono"])) ){
            $errors[] = "El telefono es obligatorio";
        }
        if(array_key_exists("edad",$data)){
           if(filter_var($data["edad"], FILTER_VALIDATE_INT) ===false){
            $errors[] = "El campo edad debe ser de tipo entero";
           }
        }
        if(isset($data["penalizado"]) && !is_bool($data["penalizado"])){
            $errors[]="El campo penalizado debe se ser true o false";
        }
        else if(array_key_exists("penalizado", $data)){
            if (!is_bool($data["penalizado"])){
                $errors[] = "El socio debe estar penalizado (true) o no (false)";
            }
        }
        return $errors;
    }
    
}


?>