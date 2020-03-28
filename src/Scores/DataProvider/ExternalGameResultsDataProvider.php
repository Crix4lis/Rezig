<?php

declare(strict_types=1);

namespace App\Rezig\Scores\DataProvider;

use App\Rezig\Scores\DataProvider\Sorter\SorterInterface;
use App\Rezig\Scores\Infrastructure\Http\Api\PrivateJacekApiClient;
use App\Rezig\Scores\Model\GameResult;
use App\Rezig\Scores\Model\PlayerResult;
use App\Rezig\Scores\DataProvider\Exception\ResourceNotFound;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ExternalGameResultsDataProvider implements GameResultsDataProviderInterface
{
    private PrivateJacekApiClient $client;
    private SorterInterface $sorter;

    public function __construct(PrivateJacekApiClient $apiClient, SorterInterface $sorter)
    {
        $this->client = $apiClient;
        $this->sorter = $sorter;
    }

    /**
     * @param string      $gameId
     * @param string|null $sortByDate
     * @param string|null $sortByScore
     *
     * @return GameResult
     *
     * @throws ResourceNotFound
     *
     * @throws ClientExceptionInterface 4xx codes
     * @throws RedirectionExceptionInterface 3xx codes
     * @throws ServerExceptionInterface 5xx codes
     * @throws TransportExceptionInterface network problem
     * @throws TransportExceptionInterface
     * @throws \InvalidArgumentException
     * @throws \Exception cannot generate datetime
     */
    public function getResultsByGameId(
        string $gameId,
        string $sortByDate = null,
        string $sortByScore = null
    ): GameResult
    {
        $results = $this->client->getGameResultById($gameId);
        $results = $this->sortByDate($results, $sortByDate);
        $results = $this->sortByScore($results, $sortByScore);

        /** @var PlayerResult[] $playerResults */
        $playerResults = [];
        foreach ($results as $result) {
            $playerResults[] = new PlayerResult(
                $result['id'],
                $result['user']['name'],
                $result['user']['id'],
                $result['score'],
                new \DateTimeImmutable($result['finished_at']),
            );
        }

        return new GameResult($gameId, $playerResults);
    }

    private function sortByDate(array $data, ?string $byOrder): array
    {
        if ($byOrder !== null) {
            $data = $this->sorter->sortByDate($data, $byOrder);
        }

        return $data;
    }

    private function sortByScore(array $data, ?string $byOrder): array
    {
        if ($byOrder !== null) {
            $data = $this->sorter->sortByScore($data, $byOrder);
        }

        return $data;
    }
}
