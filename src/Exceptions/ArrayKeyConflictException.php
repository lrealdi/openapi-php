<?php

namespace erasys\OpenApi\Exceptions;

use LogicException;
use Exception;

class ArrayKeyConflictException extends LogicException implements OpenApiException
{
    public function __construct($property, $code = 0, Exception $previous = null)
    {
        $message = 'The %s key has been already assigned and cannot be replaced.';
        parent::__construct(sprintf($message, $property), $code, $previous);
    }
}
