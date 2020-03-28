<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Infrastructure\Http\Parser\Exception;

class MissingParserException extends ParserException
{
    public function __construct(string $missingType)
    {
        parent::__construct(sprintf('Missing parser for type "%s"', $missingType));
    }
}
