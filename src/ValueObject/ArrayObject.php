<?php

declare(strict_types=1);

namespace Phprise\Common\ValueObject;

class ArrayObject extends \ArrayObject
{
    public function __construct(array $array = [])
    {
        parent::__construct($array, \ArrayObject::ARRAY_AS_PROPS);
    }

    public function toArray(): array
    {
        return (array) $this;
    }

    public function __toString(): string
    {
        return json_encode($this->toArray(), JSON_THROW_ON_ERROR);
    }

    public function replaceKey(string|int $oldKey, string|int $newKey): void
    {
        if (array_key_exists($oldKey, $this->getArrayCopy())) {
            $this[$newKey] = $this[$oldKey];
            unset($this[$oldKey]);
        }
    }

    public function replaceKeys(array $replacements): void
    {
        foreach ($replacements as $oldKey => $newKey) {
            $this->replaceKey($oldKey, $newKey);
        }
    }
}