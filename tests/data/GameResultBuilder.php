<?php

declare(strict_types=1);

namespace Tests\Rezig\data;

use App\Rezig\Scores\Model\GameResult;
use App\Rezig\Scores\Model\PlayerResult;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\PersistentCollection;

class GameResultBuilder
{
    /** @var PlayerResult[]|PersistentCollection */
    public $playersResults;
    public string $uuid;

    public function withPlayer(PlayerResult $playerResult): self
    {
        $this->playersResults = [$playerResult];

        return $this;
    }

    public function addPlayer(PlayerResult $playerResult): self
    {
        $this->playersResults[] = $playerResult;

        return $this;
    }

    public function withUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function build(): GameResult
    {
        return new GameResult($this->uuid, $this->playersResults);
    }

    public function buildWithArrayCollection(): GameResult
    {
        return new GameResult($this->uuid, new ArrayCollection($this->playersResults));
    }

    public static function gameResult(): self
    {
        return new self();
    }

    private function __construct()
    {
        $this->playersResults = (PlayerResultBuilder::playerResult())->build();
        $this->uuid = '2a708bc2-452e-4826-8b07-69653181d178';
    }
}
