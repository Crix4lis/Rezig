<?php

declare(strict_types=1);

namespace Tests\Rezig\unit\Scores\Converter;

use App\Rezig\Scores\Converter\Converter;
use App\Rezig\Scores\Converter\Parser\JsonParser;
use App\Rezig\Scores\Model\GameResult;
use PHPUnit\Framework\TestCase;
use Tests\Rezig\data\ImmutableDateTimeMotherObject;
use Tests\Rezig\data\PlayerResultBuilder;

class ConverterTest extends TestCase
{
    public function objectDataProvider(): array
    {
        return [
            'containing one player' => [
                'gameUuid',
                [
                    PlayerResultBuilder::playerResult()
                    ->withUuid('firstUuid')
                    ->withScore(50)
                    ->withFinishedAtAsString(ImmutableDateTimeMotherObject::createFirstAsString())
                    ->withPlayerUuid('p1Uuid')
                    ->withPlayerName('First Name')
                    ->build(),
                ],
                '{"gameUuid":"gameUuid","playerScores":[{"uuid":"firstUuid","score":50,"finishedAt":"2020-02-28T00:09:42+0000","playerUuid":"p1Uuid","playerName":"First Name"}]}'
            ],
            'containing two players' => [
                'gameUuid2',
                [
                    PlayerResultBuilder::playerResult()
                    ->withUuid('firstUuid')
                    ->withScore(50)
                    ->withFinishedAtAsString(ImmutableDateTimeMotherObject::createFirstAsString())
                    ->withPlayerUuid('p1Uuid')
                    ->withPlayerName('First Name')
                    ->build(),
                    PlayerResultBuilder::playerResult()
                    ->withUuid('secondUuid')
                    ->withScore(100)
                    ->withFinishedAtAsString(ImmutableDateTimeMotherObject::createSecondAsString())
                    ->withPlayerUuid('p1Uuid2')
                    ->withPlayerName('Second Name')
                    ->build(),
                ],
                '{"gameUuid":"gameUuid2","playerScores":[{"uuid":"firstUuid","score":50,"finishedAt":"2020-02-28T00:09:42+0000","playerUuid":"p1Uuid","playerName":"First Name"},{"uuid":"secondUuid","score":100,"finishedAt":"2020-03-28T00:09:42+0000","playerUuid":"p1Uuid2","playerName":"Second Name"}]}'
            ],
        ];
    }

    /**
     * @dataProvider objectDataProvider
     */
    public function testReturnsJsonStringFromObject($gameUuid, $playerResults, $expectedString): void
    {
        $converter = new Converter(new JsonParser()); //not mocked on purpose
        $result = $converter->getData(new GameResult($gameUuid, $playerResults));

        $this->assertEquals($expectedString, $result);
    }
}
