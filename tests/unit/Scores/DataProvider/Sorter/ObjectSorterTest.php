<?php

declare(strict_types=1);

namespace Tests\Rezig\unit\Scores\DataProvider\Sorter;

use App\Rezig\Scores\DataProvider\Sorter\ObjectSorter;
use PHPUnit\Framework\TestCase;
use Tests\Rezig\data\ImmutableDateTimeMotherObject;
use Tests\Rezig\data\PlayerResultBuilder;

class ObjectSorterTest extends TestCase
{
    public function unsortedByScoreObjectsDataProvider(): array
    {
        return [
            'ascending' => [
                [50, 0, 100],
                'asc',
                [0, 50, 100]
            ],
            'descending' => [
                [50, 0, 100],
                'dsc',
                [100, 50, 0]
            ]
        ];
    }

    public function unsortedByDateObjectsDataProvider(): array
    {
        return [
            'ascending' => [
                [
                    ImmutableDateTimeMotherObject::createSecondAsString(),
                    ImmutableDateTimeMotherObject::createFirstAsString(),
                    ImmutableDateTimeMotherObject::createLastAsString()
                ],
                'asc',
                [
                    ImmutableDateTimeMotherObject::createFirstAsString(),
                    ImmutableDateTimeMotherObject::createSecondAsString(),
                    ImmutableDateTimeMotherObject::createLastAsString()
                ]
            ],
            'descending' => [
                [
                    ImmutableDateTimeMotherObject::createSecondAsString(),
                    ImmutableDateTimeMotherObject::createFirstAsString(),
                    ImmutableDateTimeMotherObject::createLastAsString()
                ],
                'dsc',
                [
                    ImmutableDateTimeMotherObject::createLastAsString(),
                    ImmutableDateTimeMotherObject::createSecondAsString(),
                    ImmutableDateTimeMotherObject::createFirstAsString(),
                ]
            ]
        ];
    }

    /**
     * @dataProvider unsortedByScoreObjectsDataProvider
     */
    public function testSortsByScores($scores, $sortType, $sortedScores): void
    {
        $toSort = $this->buildPlayersToSortByScore($scores);
        $expectedSorted = $this->buildPlayersToSortByScore($sortedScores);

        $sorter = new ObjectSorter();
        $sorted = $sorter->sortByScore($toSort, $sortType);

        $this->assertEquals($expectedSorted, $sorted);
    }

    /**
     * @dataProvider unsortedByDateObjectsDataProvider
     */
    public function testSortsByDate($dates, $sortType, $sortedDates): void
    {
        $toSort = $this->buildPlayersToSortByDate($dates);
        $expectedSorted = $this->buildPlayersToSortByDate($sortedDates);

        $sorter = new ObjectSorter();
        $sorted = $sorter->sortByDate($toSort, $sortType);

        $this->assertEquals($expectedSorted, $sorted);
    }

    public function testThrowsExceptionWhenTriesToSortByDateWithUnknownOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sorter = new ObjectSorter();
        $sorter->sortByDate([], 'desc');
    }

    public function testThrowsExceptionWhenTriesToSortByScoreWithUnknownOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sorter = new ObjectSorter();
        $sorter->sortByScore([], 'desc');
    }

    public function testThrowsExceptionWhenTriesToSortArrayOfInvalidTypeByScore(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sorter = new ObjectSorter();
        $sorter->sortByScore([1, 2], 'asc');
    }

    public function testThrowsExceptionWhenTriesToSortArrayOfInvalidTypeByDate(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sorter = new ObjectSorter();
        $sorter->sortByScore(
            [
                ImmutableDateTimeMotherObject::createFirstAsString(),
                ImmutableDateTimeMotherObject::createLastAsString()
            ],
            'asc'
        );
    }

    private function buildPlayersToSortByScore($scores): array
    {
        $players = [];
        foreach ($scores as $score) {
            $players[] = PlayerResultBuilder::playerResult()
                ->withUuid('test')
                ->withScore($score)
                ->withFinishedAtAsString(ImmutableDateTimeMotherObject::createSecondAsString())
                ->withPlayerUuid('test')
                ->withPlayerName('test')
                ->build();
        }

        return $players;
    }

    private function buildPlayersToSortByDate($dates): array
    {
        $players = [];
        foreach ($dates as $date) {
            $players[] = PlayerResultBuilder::playerResult()
                ->withUuid('test')
                ->withScore(5)
                ->withFinishedAtAsString($date)
                ->withPlayerUuid('test')
                ->withPlayerName('test')
                ->build();
        }

        return $players;
    }
}
