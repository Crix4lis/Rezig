<?php

declare(strict_types=1);

namespace App\Rezig\Scores\DataProvider\Sorter;

use App\Rezig\Scores\Model\PlayerResult;
use Webmozart\Assert\Assert;

class ObjectSorter implements SorterInterface
{
    /**
     * @param PlayerResult[] $data
     * @param string         $order
     *
     * @return PlayerResult[]
     *
     * @throws \InvalidArgumentException
     */
    public function sortByDate(array $data, string $order): array
    {
        Assert::oneOf($order, self::ALLOWED_SORT_ORDER);
        Assert::allIsInstanceOf($data, PlayerResult::class);

        //ascending
        usort($data, function($a, $b) {
            /** @var PlayerResult $a */
            /** @var PlayerResult $b */
            return strcmp($a->getFinishedAtAsString(), $b->getFinishedAtAsString());
        });

        if ($order === self::SORT_DSC) {
            $data = array_reverse($data);
        }

        return $data;
    }

    /**
     * @param array  $data
     * @param string $order
     *
     * @return PlayerResult[]
     *
     * @throws \InvalidArgumentException
     */
    public function sortByScore(array $data, string $order): array
    {
        Assert::oneOf($order, self::ALLOWED_SORT_ORDER);
        Assert::allIsInstanceOf($data, PlayerResult::class);

        //ascending
        usort($data, function($a, $b) {
            /** @var PlayerResult $a */
            /** @var PlayerResult $b */
            $aa = $a->getScore();
            $bb = $b->getScore();

            return $aa <=> $bb;
        });

        if ($order === self::SORT_DSC) {
            $data = array_reverse($data);
        }

        return $data;
    }
}
