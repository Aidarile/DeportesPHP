<?php

//6a. Controllar excepciones

class ErrorHandler {

    public static function handleError(Throwable $exception) : void {
        http_response_code(500);  // <- error interno
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }

    public static function errorHandler(int $errno, string $errstr, string $errfile, int $errline) : bool {
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
?>