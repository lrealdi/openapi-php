<?php

namespace erasys\OpenApi\Spec\v3;

use ArrayAccess;
use erasys\OpenApi\Exceptions\ArrayKeyConflictException;
use erasys\OpenApi\Exceptions\UndefinedPropertyException;
use erasys\OpenApi\Exceptions\UnsupportedTypeException;
use erasys\OpenApi\ExtensionProperty;
use erasys\OpenApi\RawValue;
use JsonSerializable;
use stdClass;
use Symfony\Component\Yaml\Yaml;

/**
 * Base class for all Open API models.
 *
 * Note that defining or accessing properties dynamically is not supported by design
 * to avoid invalid schema generation. If you need to use specification extensions,
 * you can always extend the classes and add there your own properties.
 *
 */
abstract class AbstractObject implements ArrayAccess, JsonSerializable
{
    /**
     * @param array $properties
     */
    public function __construct(array $properties = [])
    {
        foreach ($properties as $k => $v) {
            $this->$k = $v;
        }
    }

    /**
     * @param string $name
     */
    final public function __get($name)
    {
        throw new UndefinedPropertyException(static::class . "::\${$name}", 1);
    }

    /**
     * @param string $name
     * @param mixed  $value
     */
    final public function __set($name, $value)
    {
        throw new UndefinedPropertyException(static::class . "::\${$name}", 2);
    }

    /**
     * @param string $offset
     *
     * @return bool
     */
    final public function offsetExists($offset)
    {
        return isset($this->$offset);
    }

    /**
     * @param string $offset
     *
     * @return mixed
     */
    final public function offsetGet($offset)
    {
        return $this->$offset;
    }

    /**
     * @param string $offset
     * @param mixed  $value
     */
    final public function offsetSet($offset, $value)
    {
        $this->$offset = $value;
    }

    /**
     * @param string $offset
     */
    final public function offsetUnset($offset)
    {
        unset($this->$offset);
    }

    /**
     * @param mixed $value
     *
     * @return array|mixed
     */
    private function exportValue($value)
    {
        if ($value instanceof RawValue) {
            // Unwrap value and return it raw, as it is.
            return $value->getValue();
        }

        if ($value instanceof AbstractObject) {
            return $value->toArray();
        }

        if ($value instanceof ExtensionProperty) {
            return [
                $value->getName() => $this->exportValue($value->getValue()),
            ];
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                // Take key and value from extension property definition
                if ($v instanceof ExtensionProperty) {
                    $k = $v->getName();
                    $v = $this->exportValue($v->getValue());
                }
                // Ignore null properties
                if (is_null($v)) {
                    continue;
                }
                if (in_array($k, ['xml'])) {
                    $result[$k] = $this->exportValue($v);
                    continue;
                }
                // Transform extension property names using the 'x-respecT_caseFormat'
                if (preg_match('/^x/i', $k)) {
                    $k = 'x-' . preg_replace('/^(x[-]?)/i', '', str_replace('_', '-', $k));
                }
                // Transform reference property names
                if ($k === 'ref') {
                    $k = '$ref';
                }
                if (isset($result[$k])) {
                    throw new ArrayKeyConflictException($k);
                }
                $result[$k] = $this->exportValue($v);
            }
            return $result;
        }

        if (!is_null($value) && !is_scalar($value)) {
            throw new UnsupportedTypeException(is_object($value) ? get_class($value) : gettype($value));
        }

        return $value;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $getPublicVars = (function ($that) {
            // Only public variables
            return get_object_vars($that);
        });
        $vars = $getPublicVars($this);

        return $this->exportValue($vars);
    }

    /**
     * @param int $options
     *
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }

    /**
     * Returns a plain object
     *
     * @return object|stdClass
     */
    public function toObject()
    {
        return json_decode($this->toJson());
    }

    /**
     * @param int $inline
     * @param int $indentation
     * @param int $flags
     * @return string
     */
    public function toYaml(
        $inline = 10,
        $indentation = 2,
        $flags = 0
    ) {
        return Yaml::dump($this->toArray(), $inline, $indentation, $flags);
    }

    /**
     * Returns a value that is suitable for JSON serialization,
     * in order to be able to export empty objects correctly, so they
     * won't be treated as empty arrays.
     *
     * @return array|stdClass
     */
    public function jsonSerialize()
    {
        $properties = $this->toArray();
        return empty($properties) ? new stdClass() : $properties;
    }

    public function __toString()
    {
        return $this->toJson();
    }
}
