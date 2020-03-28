<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Infrastructure\Http\Parser;

use App\Rezig\Scores\Infrastructure\Http\Parser\Exception\MissingParserException;
use Webmozart\Assert\Assert;

class ParserContext
{
    /** @var ParserInterface[] */
    private array $parsers = [];

    /**
     * @param ParserInterface[] $parsers of ['contentType' => $parser]
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $parsers)
    {
        Assert::allIsInstanceOf($parsers, ParserInterface::class);
        $this->parsers = $parsers;
    }

    /**
     * @param string $content
     * @param string $contentType
     *
     * @return array
     *
     * @throws MissingParserException
     */
    public function parse(string $content, string $contentType): array
    {
        if (false === array_key_exists($contentType, $this->parsers)) {
            throw new MissingParserException($contentType);
        }

        return $this->parsers[$contentType]->parse($content);
    }
}
