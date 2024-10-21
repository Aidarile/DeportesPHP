<?php

//3. Crear el controlador para cada endpoint

class PistasController {

    public function __construct(private PistasGateway $gatewayPistas) {

    }

    public function processRequestPistas(string $method, ?string $id): void {
        if ($id != null) {
            //Procesar petición de recurso
            $this -> processResourceRequest($method, $id);
        } else {
            //Procesar petición a la colección
            $this -> processCollectionRequest($method);
        }
    }


//4. Separar el proceso de Resource y Collection

private function processResourceRequest(string $method, string $id): void {

}

//7. Procesar las peticiones: Collection GET

private function processCollectionRequest(string $method) {
    switch ($method) {
        case "GET":
            echo json_encode($this -> gateway -> getAll());
        break;

        case "POST":
            $data = (array) json_decode (file_get_contents("php://input"), true);

            $id = $this -> gateway -> create($data);
            http_response_code(201); //<- se ha creado el elemento
            echo json_encode([
                "message" => "Pista creada",
                "id" => $id
            ]);
        break;

        default:
            http_response_code(405); //<- metodo no permitido
            header("Allow: GET, POST");  //<- informa de las opciones disponibles
        break;
    }
}

}
?>