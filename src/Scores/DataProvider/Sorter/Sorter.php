<?php

declare(strict_types=1);

namespace App\Rezig\Scores\DataProvider\Sorter;

use Webmozart\Assert\Assert;

class Sorter implements SorterInterface
{
    public function sortByDate(array $data, string $order): array
    {
        Assert::oneOf($order, self::ALLOWED_SORT_ORDER);

        //ascending
        usort($data, function($a, $b) {
            return strcmp($a['finished_at'], $b['finished_at']);
        });

        if ($order === self::SORT_DSC) {
            $data = array_reverse($data);
        }

        return $data;
    }

    public function sortByScore(array $data, string $order): array
    {
        Assert::oneOf($order, self::ALLOWED_SORT_ORDER);

        //ascending
        usort($data, function($a, $b) {
            $aa = $a['score'];
            $bb = $b['score'];

            if ($aa === $bb) {
                return 0;
            }

            return ($aa < $bb) ? -1 : 1;
        });

        if ($order === self::SORT_DSC) {
            $data = array_reverse($data);
        }

        return $data;
    }
}
