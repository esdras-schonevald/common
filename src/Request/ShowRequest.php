<?php

declare(strict_types=1);

namespace Phprise\Common\Request;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\UriInterface;

abstract class ShowRequest extends Request
{
    public function __construct(string|UriInterface $uri, array $headers = [], string $version = '1.1')
    {
        parent::__construct('GET', $uri, $headers, null, $version);
    }
}
