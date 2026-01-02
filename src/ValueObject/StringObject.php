<?php

declare(strict_types=1);

namespace Phprise\Common\ValueObject;

use Stringable;

class StringObject implements Stringable
{
    private string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function equals(Stringable|string $other): bool
    {
        return $this->value === (string) $other;
    }

    public function toCamel(): string
    {
        return lcfirst(
            str_replace(' ', '',
                ucwords(
                    str_replace('_', ' ', $this->value)
                )
            )
        );
    }

    public function toSnake(): string
    {
      return trim(
        strtolower(
            preg_replace('/([A-Z])/', '_$1', $this->value)
          ),
        '_'
      );
    }

    public function toPascal(): string
    {
        return ucfirst(
            str_replace(' ', '',
                ucwords(
                    str_replace('_', ' ', $this->value)
                )
            )
        );
    }

    public function toKebab(): string
    {
        return trim(
            strtolower(
                preg_replace('/([A-Z])/', '-$1', $this->value)
            ),
            '-'
        );
    }

    public function toUpper(): string
    {
        return strtoupper($this->value);
    }

    public function toLower(): string
    {
        return strtolower($this->value);
    }

    public function toTitle(): string
    {
        return ucwords(strtolower($this->value));
    }
}