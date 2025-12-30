<?php

declare(strict_types=1);

namespace Phprise\Common\DTO;

use Phprise\Common\ValueObject\StringObject;
use ReflectionClass;
use RuntimeException;
use Stringable;

abstract class TransferObject implements Stringable
{
    public function toArray(): array
    {
        $ref	=	new ReflectionClass($this);
        $props	=	$ref->getProperties();
        $data	=	[];
        foreach ($props as $prop) {
            $name   =   $prop->getName();
            $value  =   $prop->getValue($this);
            if ($value instanceof TransferObject) {
                $data[$name]    =   $value->toArray();
                continue;
            }

            if ($value instanceof \DateTimeInterface) {
                $data[$name]    =   $this->dateFormat($value);
                continue;
            }

            $data[$name] = $value;
        }

        return $data;
    }

    public static function fromArray(array $array): static
    {
        $static =   new static();
        $ref    =   new ReflectionClass($static);
        $props  =   $ref->getProperties();
        $array  =   self::arrayKeysSnakeToCamel($array);

        foreach ($props as $prop) {
            $name   =   $prop->getName();
            if (!array_key_exists($name, $array)) {
                continue;
            }

            $value  =   $array[$name];

            $type   =   $prop->getType()->getName();
            if (is_array($value) && is_subclass_of($type, TransferObject::class)) {
                $value = $type::fromArray($value);
            }

            if (is_string($value) && in_array($type, ["DateTimeInterface", "DateTimeImmutable"])) {
                $value = new \DateTimeImmutable($value);
            }

            $setter     =   'set' . ucfirst($name);
            if (method_exists($static, $setter)) {
                $static->$setter($value);
                continue;
            }

            if (property_exists($setter, $name)) {
                $static->$name = $value;
            }
        }

        return $static;
    }

    public function toSnakeCaseArray(): array
    {
        $array = $this->toArray();
        return $this->arrayKeysCamelToSnake($array);
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    public static function fromJson(string $json): static
    {
        return static::fromArray(json_decode($json, true));
    }

    public function toSnakeCaseJson(): string
    {
        return json_encode($this->toSnakeCaseArray());
    }

    private static function arrayKeysSnakeToCamel(array $array): array
    {
        $camelCaseArray = [];
        foreach ($array as $key => $value) {
            $camelCaseKey = (new StringObject((string) $key))->toCamel();
            if (is_array($value)) {
                $value = self::arrayKeysSnakeToCamel($value);
            }
            $camelCaseArray[$camelCaseKey] = $value;
        }
        return $camelCaseArray;
    }

    private function arrayKeysCamelToSnake(array $array): array
    {
        $snakeCaseArray = [];
        foreach ($array as $key => $value) {
            $snakeCaseKey = (new StringObject((string) $key))->toSnake();
            if (is_array($value)) {
                $value = $this->arrayKeysCamelToSnake($value);
            }
            $snakeCaseArray[$snakeCaseKey] = $value;
        }
        return $snakeCaseArray;
    }

    /** Implicit conversion to string */
    public function __toString(): string
    {
        return $this->toJson();
    }

    /** Explicit conversion to string */
    public function toString(): string
    {
        return $this->__toString();
    }

    /**
     * Format using aliases
     *
     * Using default aliases
     *  <code>
     *      $this->format('property: :p1');
     *  </code>
     *
     * Using custom aliases
     *  <code>
     *      $this->format(
     *          queries: ['address: %address', 'date: %date'],
     *          aliases: ['address'=>'appointmentAddress', 'date'=>'appointmentDate'],
     *          separator: '<br>',
     *          marker: '%'
     *      );
     *  </code>
     *
     * @param array|string $queries The output format
     * @param array $aliases Short aliases to call the properties.
     * By default is the first letter with counter,
     * for example, if you have 3 properties, namely "name",
     * "age" and "address", you would have the following
     * aliases "n1", "a1" and "a2" respectively.
     * @param string $separator Separator of terms. Common values: '\n', '<br>', ',', '\t'
     * @param string $marker Indicates a variable into the query (Default ':')
     * @return string formated string
     */
    public function format(
        array|string $queries,
        array $aliases = [],
        string $separator = PHP_EOL,
        string $marker = ':'
    ): string {
        if (is_array($queries)) {
            $queries = implode($separator, $queries);
        }

        $array  =   $this->toArray();
        if (empty($aliases)) {
            array_map(function ($propName) use (&$aliases) {
                $count = 1;
                do {
                    $alias = substr($propName, 0, 1). $count++;
                } while(isset($aliases[$alias]));
                $aliases[$alias] = $propName;
            }, array_keys($array));
        }

        $keys       =   array_map(fn ($k) => $marker . $k, array_keys($aliases));
        $values     =   array_map(fn ($v) => $this->getAttributeAsString($v), $aliases);
        $output     =   str_replace($keys, $values, $queries);

        return $output;
    }

    /**
     * @param class-string<\T>|\T $entity
     * @return object<\T>
     */
    public function toEntity(string|object $entity): object
    {
        $instance = $entity;

        if (is_string($entity)) {
            $instance = $this->createClassFromString($entity);
        }

        $refDto     =   new ReflectionClass($this);
        $refEntity  =   new ReflectionClass($instance);
        $props      =   $refDto->getProperties();

        foreach($props as $prop) {
            $propName   =   $prop->getName();
            $propValue  =   $prop->getValue($this);
            $setter     =   'set' . ucfirst($propName);
            $propEntity =   $refEntity->getProperty($propName);
            $type       =   $propEntity->getType()->getName();

            if ($propValue === null) {
                continue;
            }

            if ($propValue instanceof TransferObject) {
                $propValue  =   $propValue->toEntity($type);
            }

            if ($refEntity->hasMethod($setter)) {
                $instance->$setter($propValue);
                continue;
            }

            if ($propEntity->isPublic()) {
                $instance->$propName = $propValue;
            }
        }

        return $instance;
    }

    protected function dateFormat(\DateTimeInterface $date): string
    {
        return $date->format(\DateTime::RFC3339);
    }

    protected function createClassFromString(string $className): object
    {
        if (!class_exists($className)) {
            throw new RuntimeException(sprintf('Entity %s not found', $className));
        }

        return new $className();
    }

    protected function getAttributeAsString(string $attributeName): string
    {
        $getter     =   'get' . ucfirst($attributeName);
        if (method_exists($this, $getter)) {
            $val    =   $this->$getter();
            return $this->getStrVal($val);
        }

        if (property_exists($this, $attributeName)) {
            $val    =   $this->$attributeName;
            return $this->getStrVal($val);
        }

        throw new RuntimeException(
            sprintf(
                'Undefined attribute %s::%s',
                get_class($this),
                $attributeName
            )
        );
    }

    protected function getStrVal(mixed $val): string
    {
        if (!is_array($val) && !is_object($val)) {
            return (string) $val;
        }

        if ($val instanceof Stringable) {
            return (string) $val;
        }

        if ($val instanceof \DateTimeInterface) {
            return $this->dateFormat($val);
        }

        return json_encode($val);
    }
}
