<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Infrastructure\Http\Api;

use App\Rezig\Scores\ContainerFactory\HttpClientFactory;
use App\Rezig\Scores\Infrastructure\Http\Parser\ParserContext;
use App\Rezig\Scores\DataProvider\Exception\ResourceNotFound;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Webmozart\Assert\Assert;

class PrivateJacekApiClient extends BaseApiClient
{
    private string $protocol;
    private string $domain;
    private string $scoresUri;

    /**
     * @param string            $protocol
     * @param string            $domain
     * @param string            $scoresUri
     * @param HttpClientFactory $httpClientFactory
     * @param ParserContext     $parserContext
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        string $protocol,
        string $domain,
        string $scoresUri,
        HttpClientFactory $httpClientFactory,
        ParserContext $parserContext
    )
    {
        parent::__construct($httpClientFactory, $parserContext);

        Assert::oneOf($protocol, self::ALLOWED_PROTOCOL_TYPES);
        $this->protocol = $protocol;
        $this->domain = $domain;
        $this->scoresUri = $scoresUri;
    }

    /**
     * @param string $gameId
     *
     * @return array
     *
     * @throws ResourceNotFound
     * @throws ClientExceptionInterface 4xx codes
     * @throws RedirectionExceptionInterface 3xx codes
     * @throws ServerExceptionInterface 5xx codes
     * @throws TransportExceptionInterface network problem
     */
    public function getGameResultById(string $gameId): array
    {
        $response = $this
            ->createClient()
            ->request('GET', $this->protocol.'://'.$this->domain.'/'.$this->scoresUri.'/'.$gameId);

        if ($response->getStatusCode() === 404) {
            throw new ResourceNotFound('Game', $gameId);
        }

        $content = $response->getContent();
        $contentType = $response->getHeaders()['content-type'][0];

        return $this->getParser()->parse($content, $contentType);
    }
}
