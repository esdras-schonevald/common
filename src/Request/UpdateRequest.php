<?php

declare(strict_types=1);

namespace App\Shared\Request;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\UriInterface;

abstract class PatchRequest extends Request
{
    public function __construct(string|UriInterface $uri, mixed $body, array $headers = [], string $version = '1.1')
    {
        parent::__construct('PATCH', $uri, $headers, $body, $version);
    }
}