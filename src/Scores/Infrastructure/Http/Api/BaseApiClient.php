<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Infrastructure\Http\Api;

use App\Rezig\Scores\ContainerFactory\HttpClientFactory;
use App\Rezig\Scores\Infrastructure\Http\Parser\ParserContext;
use Symfony\Contracts\HttpClient\HttpClientInterface;

abstract class BaseApiClient
{
    protected const ALLOWED_PROTOCOL_TYPES = ['http', 'https'];

    private HttpClientFactory $httpClientFactory;
    private ParserContext $parserContext;
    /** @var string[] */
    private array $clientOptions = [];
    private int $maxPendingPushes;
    private int $maxHostConnections;

    public function __construct(
        HttpClientFactory $httpClientFactory,
        ParserContext $parserContext,
        array $symfonyHttpClientOptions = [],
        int $symfonyHttpClientMaxHostConnections = 6,
        int $symfonyHttpClientMaxPendingPushes = 50
    )
    {
        $this->httpClientFactory = $httpClientFactory;
        $this->parserContext = $parserContext;
        $this->clientOptions = $symfonyHttpClientOptions;
        $this->maxHostConnections = $symfonyHttpClientMaxHostConnections;
        $this->maxPendingPushes = $symfonyHttpClientMaxPendingPushes;
    }

    protected function createClient(): HttpClientInterface
    {
        return $this->httpClientFactory->create(
            $this->clientOptions,
            $this->maxHostConnections,
            $this->maxPendingPushes
        );
    }

    protected function getParser(): ParserContext
    {
        return $this->parserContext;
    }
}
