<?php

declare(strict_types=1);

namespace App\Rezig\Scores\DataProvider;

use App\Rezig\Scores\Model\GameResult;
use App\Scores\DataProvider\Exception\ResourceNotFound;
use Doctrine\ODM\MongoDB\MongoDBException;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

interface GameResultsDataProviderInterface
{

    /**
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
    ): GameResult;
}
