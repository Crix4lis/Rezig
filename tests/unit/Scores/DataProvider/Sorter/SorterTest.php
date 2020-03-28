<?php

declare(strict_types=1);

namespace ATests\Rezig\unit\Scores\DataProvider\Sorter;

use App\Rezig\Scores\DataProvider\Sorter\Sorter;
use PHPUnit\Framework\TestCase;
use Tests\Rezig\data\ImmutableDateTimeMotherObject;

class SorterTest extends TestCase
{
    public function unsortedByScoreObjectsDataProvider(): array
    {
        return [
            'ascending' => [
                [['score' => 50], ['score' => 0], ['score' => 100]],
                'asc',
                [['score' => 0], ['score' => 50], ['score' => 100]],
            ],
            'descending' => [
                [['score' => 50], ['score' => 0], ['score' => 100]],
                'dsc',
                [['score' => 100], ['score' => 50], ['score' => 0]],
            ]
        ];
    }

    public function unsortedByDateObjectsDataProvider(): array
    {
        return [
            'ascending' => [
                [
                    ['finished_at' => ImmutableDateTimeMotherObject::createSecondAsString()],
                    ['finished_at' => ImmutableDateTimeMotherObject::createFirstAsString()],
                    ['finished_at' => ImmutableDateTimeMotherObject::createLastAsString()],
                ],
                'asc',
                [
                    ['finished_at' => ImmutableDateTimeMotherObject::createFirstAsString()],
                    ['finished_at' => ImmutableDateTimeMotherObject::createSecondAsString()],
                    ['finished_at' => ImmutableDateTimeMotherObject::createLastAsString()]
                ]
            ],
            'descending' => [
                [
                    ['finished_at' => ImmutableDateTimeMotherObject::createSecondAsString()],
                    ['finished_at' => ImmutableDateTimeMotherObject::createFirstAsString()],
                    ['finished_at' => ImmutableDateTimeMotherObject::createLastAsString()]
                ],
                'dsc',
                [
                    ['finished_at' => ImmutableDateTimeMotherObject::createLastAsString()],
                    ['finished_at' => ImmutableDateTimeMotherObject::createSecondAsString()],
                    ['finished_at' => ImmutableDateTimeMotherObject::createFirstAsString()],
                ]
            ]
        ];
    }

    /**
     * @dataProvider unsortedByScoreObjectsDataProvider
     */
    public function testSortsByScores($scores, $sortType, $sortedScores): void
    {
        $sorter = new Sorter();
        $sorted = $sorter->sortByScore($scores, $sortType);

        $this->assertEquals($sortedScores, $sorted);
    }


    /**
     * @dataProvider unsortedByDateObjectsDataProvider
     */
    public function testSortsByDate($dates, $sortType, $sortedDates): void
    {
        $sorter = new Sorter();
        $sorted = $sorter->sortByDate($dates, $sortType);

        $this->assertEquals($sortedDates, $sorted);
    }

    public function testThrowsExceptionWhenTriesToSortByDateWithUnknownOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sorter = new Sorter();
        $sorter->sortByDate([], 'desc');
    }

    public function testThrowsExceptionWhenTriesToSortByScoreWithUnknownOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $sorter = new Sorter();
        $sorter->sortByScore([], 'desc');
    }
}
