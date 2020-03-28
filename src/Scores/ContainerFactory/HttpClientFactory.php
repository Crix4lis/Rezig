<?php

declare(strict_types=1);

namespace App\Rezig\Scores\ContainerFactory;

use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class HttpClientFactory
{
    public function create(
        array $symfonyHttpClientOptions = [],
        int $maxHostConnections = 6,
        int $maxPendingPushes = 50
    ): HttpClientInterface
    {
        return HttpClient::create($symfonyHttpClientOptions, $maxHostConnections, $maxPendingPushes);
    }
}
