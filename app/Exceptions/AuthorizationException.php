<?php

namespace App\Exceptions;

use Exception;

class AuthorizationException extends Exception
{
    private $statusCode = 403;

    public function __construct($message = null, \Exception $previous = null, $code = 0)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }
}
