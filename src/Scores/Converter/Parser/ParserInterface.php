<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Converter\Parser;

interface ParserInterface
{
    public function parse(array $data): string;
}
