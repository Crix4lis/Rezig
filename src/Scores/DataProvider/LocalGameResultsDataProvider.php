<?php

declare(strict_types=1);

namespace App\Rezig\Scores\DataProvider;

use App\Rezig\Scores\DataProvider\Sorter\SorterInterface;
use App\Rezig\Scores\Infrastructure\Storage\Mongo\ResultsRepository;
use App\Rezig\Scores\Model\GameResult;
use App\Rezig\Scores\DataProvider\Exception\ResourceNotFound;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class LocalGameResultsDataProvider implements GameResultsDataProviderInterface
{
    private GameResultsDataProviderInterface $externalGameResultsDataProvider;
    private ResultsRepository $resultsRepository;
    private SorterInterface $sorter;

    public function __construct(
        GameResultsDataProviderInterface $externalGameResultsDataProvider,
        ResultsRepository $resultsRepository,
        SorterInterface $sorter
    )
    {
        $this->externalGameResultsDataProvider = $externalGameResultsDataProvider;
        $this->resultsRepository = $resultsRepository;
        $this->sorter = $sorter;
    }

    /**
     * TODO: move sorting to mongo
     *
     * @param string      $gameId
     * @param string|null $sortByDate
     * @param string|null $sortByScore
     *
     * @return GameResult
     *
     * @throws ClientExceptionInterface 4xx codes
     * @throws RedirectionExceptionInterface 3xx codes
     * @throws ServerExceptionInterface 5xx codes
     * @throws TransportExceptionInterface network problem
     * @throws MongoDBException
     * @throws ResourceNotFound
     * @throws \InvalidArgumentException
     */
    public function getResultsByGameId(
        string $gameId,
        string $sortByDate = null,
        string $sortByScore = null
    ): GameResult {
        try {
            $result =  $this->resultsRepository->getByGameId($gameId);

            if ($sortByDate !== null) {
                $sortedPlayers = $this->sorter->sortByDate($result->getPlayersResults()->toArray(), $sortByDate);
                return new GameResult($result->getUuid(), $sortedPlayers);
            }

            if ($sortByScore !== null) {
                $sortedPlayers = $this->sorter->sortByScore($result->getPlayersResults()->toArray(), $sortByScore);
                return new GameResult($result->getUuid(), $sortedPlayers);
            }

            return $result;
        } catch (ResourceNotFound $e) {
            $result = $this->externalGameResultsDataProvider->getResultsByGameId($gameId, $sortByDate, $sortByScore);
            $this->resultsRepository->add($result);
        }

        return $result;
    }
}
