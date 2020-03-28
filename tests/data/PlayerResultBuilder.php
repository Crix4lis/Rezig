<?php

declare(strict_types=1);

namespace Tests\Rezig\data;

use App\Rezig\Scores\Model\PlayerResult;

class PlayerResultBuilder
{
    private string $uuid;
    private string $playerName;
    private string $playerUuid;
    private int $score;
    private \DateTimeImmutable $finishedAt;

    public function withUuid(string $uuid): self
    {
        $this->uuid = $uuid;

        return $this;
    }

    public function withPlayerName(string $playerName): self
    {
        $this->playerName = $playerName;

        return $this;
    }

    public function withPlayerUuid(string $playerUuid): self
    {
        $this->playerUuid = $playerUuid;

        return $this;
    }

    public function withScore(int $score): self
    {
        $this->score = $score;

        return $this;
    }

    public function withFinishedAtAsString(string $finishedAd): self
    {
        $this->finishedAt = new \DateTimeImmutable($finishedAd);

        return $this;
    }

    public static function playerResult(): self
    {
        return new self();
    }

    public function build(): PlayerResult
    {
        return new PlayerResult($this->uuid, $this->playerName, $this->playerUuid, $this->score, $this->finishedAt);
    }

    private function __construct()
    {
        $this->uuid = 'a227380b-890b-4265-b26a-d5c8849c281a';
        $this->playerName = 'Some Name';
        $this->playerUuid = '9f4139ac-1b7a-43e2-95e3-a94f94b17571';
        $this->score = 10;
        $this->finishedAt = ImmutableDateTimeMotherObject::createFirst();
    }
}
