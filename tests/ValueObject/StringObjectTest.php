<?php

declare(strict_types=1);

namespace Phprise\Common\Test\ValueObject;

use PHPUnit\Framework\TestCase;
use Phprise\Common\ValueObject\StringObject;

class StringObjectTest extends TestCase
{
    public function testStringManipulation(): void
    {
        $str = new StringObject('hello world');
        $this->assertEquals('hello world', (string) $str);
        $this->assertEquals('hello world', $str->getValue());

        $this->assertEquals('HelloWorld', $str->toPascal());
        $this->assertEquals('helloWorld', $str->toCamel());
        $this->assertEquals('HELLO WORLD', $str->toUpper());
        $this->assertEquals('Hello World', $str->toTitle());
    }

    public function testSnakeCase(): void
    {
        $str = new StringObject('HelloWorld');
        $this->assertEquals('hello_world', $str->toSnake());
    }

    public function testKebabCase(): void
    {
        $str = new StringObject('HelloWorld');
        $this->assertEquals('hello-world', $str->toKebab());
    }

    public function testEquals(): void
    {
        $str1 = new StringObject('test');
        $str2 = new StringObject('test');
        $str3 = new StringObject('other');

        $this->assertTrue($str1->equals($str2));
        $this->assertTrue($str1->equals('test'));
        $this->assertFalse($str1->equals($str3));
    }
}
