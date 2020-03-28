<?php

declare(strict_types=1);

namespace Tests\Rezig\unit\Scores\DataProvider;

use App\Rezig\Scores\DataProvider\Exception\ResourceNotFound;
use App\Rezig\Scores\DataProvider\GameResultsDataProviderInterface;
use App\Rezig\Scores\DataProvider\LocalGameResultsDataProvider;
use App\Rezig\Scores\DataProvider\Sorter\SorterInterface;
use App\Rezig\Scores\Infrastructure\Storage\Mongo\ResultsRepository;
use App\Rezig\Scores\Model\GameResult;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\Rezig\data\GameResultBuilder;
use Tests\Rezig\data\PlayerResultBuilder;

class LocalGameResultsDataProviderTest extends TestCase
{
    /** @var ObjectProphecy|SorterInterface */
    private ObjectProphecy $sorter;
    /** @var ObjectProphecy|ResultsRepository */
    private ObjectProphecy $repository;
    /** @var ObjectProphecy|GameResultsDataProviderInterface */
    private ObjectProphecy $apiDataProvider;

    public function setUp(): void
    {
        $this->apiDataProvider = $this->prophesize(GameResultsDataProviderInterface::class);
        $this->repository = $this->prophesize(ResultsRepository::class);
        $this->sorter = $this->prophesize(SorterInterface::class);
    }

    public function gameDataDateProvider(): array
    {
        return [
            'two players' => [
                '123',
                [
                    [
                        'id' => 'a227380b-890b-4265-b26a-d5c8849c281a',
                        'score' => 5,
                        'finished_at' => '2020-02-27T11:25:00+00:00',
                        'user' => [
                            'id' => '9f4139ac-1b7a-43e2-95e3-a94f94b17571',
                            'name' => 'Leona Everett',
                        ],
                    ],
                    [
                        'id' => 'aaaaa',
                        'score' => 3,
                        'finished_at' => '2090-02-27T11:25:00+00:00',
                        'user' => [
                            'id' => 'ccccccccccc',
                            'name' => 'My Name',
                        ]
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider gameDataDateProvider
     */
    public function testGetsResultsFromLocalRepository($gameId, $resultData): void
    {
        $expected = $repoReturn = $this->prepareGameResultObject($gameId, $resultData);
        $this->repository->getByGameId($gameId)->willReturn($repoReturn);

        $dataProvider = new LocalGameResultsDataProvider(
            $this->apiDataProvider->reveal(),
            $this->repository->reveal(),
            $this->sorter->reveal()
        );
        $returned = $dataProvider->getResultsByGameId($gameId);

        $this->assertEquals($expected, $returned);
    }

    /**
     * @dataProvider gameDataDateProvider
     */
    public function testGetsResultsFromLocalRepositorySortedByDate($gameId, $resultData): void
    {
        $repoReturn = $this->prepareGameResultObject($gameId, $resultData);
        $expected = $this->prepareGameResultObject($gameId, $resultData, false);
        $this->repository->getByGameId($gameId)->willReturn($repoReturn);
        $this->sorter->sortByDate($repoReturn->getPlayersResults()->toArray(), 'asc')
            ->willReturn($repoReturn->getPlayersResults()->toArray())
            ->shouldBeCalled();

        $dataProvider = new LocalGameResultsDataProvider(
            $this->apiDataProvider->reveal(),
            $this->repository->reveal(),
            $this->sorter->reveal()
        );
        $returned = $dataProvider->getResultsByGameId($gameId, 'asc');

        $this->assertEquals($expected, $returned);
    }

    /**
     * @dataProvider gameDataDateProvider
     */
    public function testGetsResultsFromLocalRepositorySortedByScore($gameId, $resultData): void
    {
        $repoReturn = $this->prepareGameResultObject($gameId, $resultData);
        $expected = $this->prepareGameResultObject($gameId, $resultData, false);
        $this->repository->getByGameId($gameId)->willReturn($repoReturn);
        $this->sorter->sortByScore($repoReturn->getPlayersResults()->toArray(), 'asc')
            ->willReturn($repoReturn->getPlayersResults()->toArray())
            ->shouldBeCalled();

        $dataProvider = new LocalGameResultsDataProvider(
            $this->apiDataProvider->reveal(),
            $this->repository->reveal(),
            $this->sorter->reveal()
        );
        $returned = $dataProvider->getResultsByGameId($gameId, null, 'asc');

        $this->assertEquals($expected, $returned);
    }

    /**
     * @dataProvider gameDataDateProvider
     */
    public function testGetsResultsFromApiWhenResultsNotFoundInRepository($gameId, $resultData): void
    {
        $expected = $apiReturn = $this->prepareGameResultObject($gameId, $resultData, false);
        $this->repository->getByGameId($gameId)->willThrow(new ResourceNotFound('', ''));
        $this->apiDataProvider->getResultsByGameId($gameId, null, null)->willReturn($apiReturn);
        $this->repository->add($apiReturn)->shouldBeCalled();

        $dataProvider = new LocalGameResultsDataProvider(
            $this->apiDataProvider->reveal(),
            $this->repository->reveal(),
            $this->sorter->reveal()
        );
        $returned = $dataProvider->getResultsByGameId($gameId);

        $this->assertEquals($expected, $returned);
    }

    private function prepareGameResultObject(
        string $gameId,
        array $playersData,
        bool $withDoctirneCollection = true
    ): GameResult
    {
        $expectedPlayers = [];
        foreach ($playersData as $player)
        {
            $expectedPlayers[] = PlayerResultBuilder::playerResult()
                ->withUuid($player['id'])
                ->withFinishedAtAsString($player['finished_at'])
                ->withScore($player['score'])
                ->withPlayerName($player['user']['name'])
                ->withPlayerUuid($player['user']['id'])
                ->build();
        }

        $gameBuilder = GameResultBuilder::gameResult()
            ->withUuid($gameId)
            ->withPlayer(array_shift($expectedPlayers));

        foreach ($expectedPlayers as $ep) {
            $gameBuilder->addPlayer($ep);
        }

        if (false === $withDoctirneCollection) {
            return $gameBuilder->build();
        }

        return $gameBuilder->buildWithArrayCollection();
    }
}
