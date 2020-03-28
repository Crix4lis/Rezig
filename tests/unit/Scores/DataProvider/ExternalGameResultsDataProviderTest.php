<?php

declare(strict_types=1);

namespace Tests\Rezig\unit\Scores\DataProvider;

use App\Rezig\Scores\DataProvider\Exception\ResourceNotFound;
use App\Rezig\Scores\DataProvider\ExternalGameResultsDataProvider;
use App\Rezig\Scores\DataProvider\Sorter\SorterInterface;
use App\Rezig\Scores\Infrastructure\Http\Api\PrivateJacekApiClient;
use App\Rezig\Scores\Model\GameResult;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Tests\Rezig\data\GameResultBuilder;
use Tests\Rezig\data\PlayerResultBuilder;

class ExternalGameResultsDataProviderTest extends TestCase
{
    /** @var ObjectProphecy|PrivateJacekApiClient  */
    private ObjectProphecy $apiClient;
    /** @var ObjectProphecy|SorterInterface  */
    private ObjectProphecy $sorter;

    public function setUp(): void
    {
        $this->apiClient = $this->prophesize(PrivateJacekApiClient::class);
        $this->sorter = $this->prophesize(SorterInterface::class);
    }

    public function apiDataDateProvider(): array
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
     * @dataProvider apiDataDateProvider
     */
    public function testReturnsGameResultWithSortingParameters($gameId, $returnedData): void
    {
        $expected = $this->prepareExpectedResult($gameId, $returnedData);
        $this->apiClient->getGameResultById($gameId)->willReturn($returnedData);

        $dataProvider = new ExternalGameResultsDataProvider(
            $this->apiClient->reveal(),
            $this->sorter->reveal()
        );
        $got = $dataProvider->getResultsByGameId($gameId);

        $this->assertEquals($got, $expected);
    }

    /**
     * @dataProvider apiDataDateProvider
     */
    public function testReturnExpectedResultWithSortParameters($gameId, $returnedData): void
    {
        $expected = $this->prepareExpectedResult($gameId, $returnedData);
        $this->apiClient->getGameResultById($gameId)->willReturn($returnedData);
        $this->sorter->sortByDate($returnedData, 'dsc')->willReturn($returnedData)->shouldBeCalledOnce();
        $this->sorter->sortByScore($returnedData, 'asc')->willReturn($returnedData)->shouldBeCalledOnce();

        $dataProvider = new ExternalGameResultsDataProvider(
            $this->apiClient->reveal(),
            $this->sorter->reveal()
        );
        $got = $dataProvider->getResultsByGameId($gameId, 'dsc', 'asc');

        $this->assertEquals($got, $expected);
    }

    public function testThrowsResourceNotFoundWhenClientCannotFindResource(): void
    {
        $this->expectException(ResourceNotFound::class);

        $this->apiClient->getGameResultById('1')->willThrow(
            new ResourceNotFound('', '')
        );
        (new ExternalGameResultsDataProvider(
            $this->apiClient->reveal(),
            $this->sorter->reveal()
        ))->getResultsByGameId('1');
    }

    private function prepareExpectedResult(string $gameId, array $playersData): GameResult
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

        return $gameBuilder->build();
    }
}
