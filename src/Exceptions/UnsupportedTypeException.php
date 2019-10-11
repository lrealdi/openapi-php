<?php

namespace erasys\OpenApi\Exceptions;

use Exception;
use UnexpectedValueException;

class UnsupportedTypeException extends UnexpectedValueException implements OpenApiException
{
    public function __construct($type, $code = 0, Exception $previous = null)
    {
        $message = 'Unsupported type: %s.';
        parent::__construct(sprintf($message, $type), $code, $previous);
    }
}
