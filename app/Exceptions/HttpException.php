<?php

namespace App\Exceptions;

class HttpException extends \Exception
{
    public function __construct($message = "", $code = 500, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => $this->getMessage(),
            'status' => $this->getCode()
        ], $this->getCode());
    }
}
