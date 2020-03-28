<?php

declare(strict_types=1);

namespace App\Rezig\Scores\ContainerFactory;

use App\Rezig\Scores\Infrastructure\Http\Parser\JsonParser;
use App\Rezig\Scores\Infrastructure\Http\Parser\ParserContext;
use App\Rezig\Scores\Infrastructure\Http\Parser\ParserInterface;

class ParserContextFactory
{
    public function create(): ParserContext
    {
        return new ParserContext($this->getParsers());
    }

    /**
     * @return ParserInterface[]
     */
    private function getParsers(): array
    {
        return [
            'application/json' => new JsonParser(),
        ];
    }
}
