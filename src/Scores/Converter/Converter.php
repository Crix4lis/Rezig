<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Converter;

use App\Rezig\Scores\Model\GameResult;
use App\Rezig\Scores\Converter\Parser\ParserInterface;

class Converter
{
    private ParserInterface $parser;

    public function __construct(ParserInterface $parser)
    {
        $this->parser = $parser;
    }

    public function getData(GameResult $gameResult): string
    {
        $results = $gameResult->getPlayersResults();

        $scores = [];
        foreach ($results as $result) {
            $scores[] = [
                'uuid' => $result->getUuid(),
                'score' => $result->getScore(),
                'finishedAt' => $result->getFinishedAtAsString(),
                'playerUuid' => $result->getPlayerUuid(),
                'playerName' => $result->getPlayerName()
            ];
        }

        $toString = [
            'gameUuid' => $gameResult->getUuid(),
            'playerScores' => $scores,
        ];

        return $this->parser->parse($toString);
    }
}
