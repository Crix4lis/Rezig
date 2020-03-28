<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Infrastructure\Http\Parser;

interface ParserInterface
{
    public function parse(string $content): array;
}
