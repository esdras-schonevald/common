<?php

declare(strict_types=1);

namespace Phprise\Common\Test\ValueObject;

use PHPUnit\Framework\TestCase;
use Phprise\Common\ValueObject\ArrayObject;

class ArrayObjectTest extends TestCase
{
    public function testArrayObjectBasics(): void
    {
        $arr = new ArrayObject(['a' => 1, 'b' => 2]);

        $this->assertEquals(1, $arr->a);
        $this->assertEquals(2, $arr->b);
        $this->assertEquals(['a' => 1, 'b' => 2], $arr->toArray());
    }

    public function testToStringJson(): void
    {
        $data = ['a' => 1];
        $arr = new ArrayObject($data);

        $this->assertJsonStringEqualsJsonString(json_encode($data), (string) $arr);
    }

    public function testReplaceKey(): void
    {
        $arr = new ArrayObject(['old' => 'value', 'keep' => 'kept']);
        $arr->replaceKey('old', 'new');

        $this->assertArrayNotHasKey('old', $arr);
        $this->assertArrayHasKey('new', $arr);
        $this->assertEquals('value', $arr['new']);
        $this->assertEquals('kept', $arr['keep']);
    }

    public function testReplaceKeys(): void
    {
        $arr = new ArrayObject(['k1' => 1, 'k2' => 2]);
        $arr->replaceKeys(['k1' => 'new1', 'k2' => 'new2']);

        $this->assertArrayNotHasKey('k1', $arr);
        $this->assertArrayHasKey('new1', $arr);
        $this->assertEquals(1, $arr['new1']);
    }
}
