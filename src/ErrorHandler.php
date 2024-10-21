<?php

//6a. Controllar excepciones

class ErrorHandler {

    public static function handleException(Throwable $exception) : void {
        http_response_code(500);  // <- error interno
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }
}
?>