<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Model;

class PlayerResult
{
    private string $uuid;
    private string $playerName;
    private string $playerUuid;
    private int $score;
    private string $finishedAt;

    public function __construct(
        string $resultUuid,
        string $playerName,
        string $playerUuid,
        int $score,
        \DateTimeImmutable $finishedAt
    ) {
        $this->uuid = $resultUuid;
        $this->playerName = $playerName;
        $this->playerUuid = $playerUuid;
        $this->score = $score;
        $this->finishedAt = $finishedAt->format( \DateTimeInterface::ISO8601);
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getScore(): int
    {
        return $this->score;
    }

    public function getFinishedAtAsString(): string
    {
        return $this->finishedAt;
    }

    public function getPlayerUuid(): string
    {
        return $this->playerUuid;
    }

    public function getPlayerName(): string
    {
        return $this->playerName;
    }
}
