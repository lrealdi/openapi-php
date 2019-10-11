<?php

namespace erasys\OpenApi\Exceptions;

use LogicException;
use Exception;

class UndefinedPropertyException extends LogicException implements OpenApiException
{
    public function __construct($property, $code = 0, Exception $previous = null)
    {
        $message = 'The %s property is not defined. Dynamic access is disabled for Open API objects.';
        parent::__construct(sprintf($message, $property), $code, $previous);
    }
}
