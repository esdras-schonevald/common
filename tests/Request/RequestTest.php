<?php

declare(strict_types=1);

namespace Phprise\Common\Test\Request;

use PHPUnit\Framework\TestCase;
use Phprise\Common\Request\StoreRequest;
use Phprise\Common\Request\UpdateRequest;
// Add other request classes if needed, but they are similar structure.
// We are testing the abstract inheritance and construction basically.

class RequestTest extends TestCase
{
    public function testStoreRequest(): void
    {
        $body = json_encode(['foo' => 'bar']);
        $req = new class('/api/resource', $body) extends StoreRequest {};

        $this->assertEquals('POST', $req->getMethod());
        $this->assertEquals('/api/resource', (string) $req->getUri());

        $this->assertEquals('bar', json_decode((string)$req->getBody(), true)['foo']);
    }

    public function testUpdateRequest(): void
    {
        $req = new class('/api/resource/1', 'body') extends UpdateRequest {};
        $this->assertEquals('PATCH', $req->getMethod());
    }

    public function testReplaceRequest(): void
    {
        $req = new class('/api/resource/1', 'body') extends \Phprise\Common\Request\ReplaceRequest {};
        $this->assertEquals('PUT', $req->getMethod());
    }

    public function testDestroyRequest(): void
    {
        $req = new class('/api/resource/1') extends \Phprise\Common\Request\DestroyRequest {};
        $this->assertEquals('DELETE', $req->getMethod());
    }

    public function testShowRequest(): void
    {
        $req = new class('/api/resource/1') extends \Phprise\Common\Request\ShowRequest {};
        $this->assertEquals('GET', $req->getMethod());
    }
}
