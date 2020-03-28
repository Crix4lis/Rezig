<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Infrastructure\Storage\Mongo;

use App\Rezig\Scores\Model\GameResult;
use App\Rezig\Scores\DataProvider\Exception\ResourceNotFound;
use Doctrine\ODM\MongoDB\DocumentManager;
use Doctrine\ODM\MongoDB\MongoDBException;
use Doctrine\ODM\MongoDB\Repository\DocumentRepository;

class ResultsRepository extends DocumentRepository
{
    public function __construct(DocumentManager $dm)
    {
        $uow = $dm->getUnitOfWork();
        $classMetaData = $dm->getClassMetadata(GameResult::class);
        parent::__construct($dm, $uow, $classMetaData);
    }

    /**
     * @param GameResult $result
     *
     * @throws MongoDBException
     */
    public function add(GameResult $result): void
    {
        $manager = $this->getDocumentManager();
        $manager->persist($result);
        $manager->flush();
    }

    /**
     * TODO: Update this method to sort
     *
     * @param string      $gameId
     * @param string|null $sortByDate
     * @param string|null $sortByScore
     *
     * @return GameResult
     *
     * @throws ResourceNotFound
     */
    public function getByGameId(string $gameId, string $sortByDate = null, string $sortByScore = null): GameResult
    {
        $manager = $this->getDocumentManager();
        /** @var GameResult|null $result */
        $result = $manager->find(GameResult::class, $gameId);

        if ($result === null) {
            throw new ResourceNotFound('Game', $gameId);
        }

        return $result;
    }
}
