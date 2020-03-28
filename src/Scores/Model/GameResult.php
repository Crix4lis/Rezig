<?php

declare(strict_types=1);

namespace App\Rezig\Scores\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ODM\MongoDB\PersistentCollection;
use Webmozart\Assert\Assert;

class GameResult
{
    /** @var PlayerResult[]|PersistentCollection */
    public $playersResults;
    public string $uuid;

    /**
     * @param string                         $gameUuid
     * @param PlayerResult[]|ArrayCollection $playerResult
     */
    public function __construct(string $gameUuid, $playerResult)
    {
        if (!($playerResult instanceof ArrayCollection)) {
            Assert::allIsInstanceOf(
                $playerResult,
                PlayerResult::class,
                'Expected Doctrine\Common\Collections\ArrayCollection or array of App\Rezig\Scores\Mode\PlayerResult');
        }
        $this->uuid = $gameUuid;
        $this->playersResults = $playerResult;
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    /**
     * @return PlayerResult[]|PersistentCollection
     */
    public function getPlayersResults()
    {
        return $this->playersResults;
    }
}
