<?php

//5c. Crear el modelo y la conexion a la BBDD

class ReservasGateway {
    private PDO $con;
    public function __construct(Database $database) {
        $this -> con = $database -> getConnection();
    }

    public function getAll() : Array {
        $sql = "SELECT * FROM reserva";
        $stmt = $this -> con -> query($sql);
        $data = [];
    
        while ($row = $stmt ->fetch(PDO::FETCH_ASSOC)) {
            $row["iluminar"] = (bool) $row["iluminar"];
            $data[] = $row;
        }
        return $data;
    }
}



?>