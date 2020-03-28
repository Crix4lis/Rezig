<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Infrastructure\Http\Parser;

class JsonParser implements ParserInterface
{
    public function parse(string $content): array
    {
        return json_decode($content, true);
    }
}
