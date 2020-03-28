<?php

declare(strict_types=1);

namespace App\Rezig\Scores\DataProvider\Sorter;

interface SorterInterface
{
    public const SORT_ASC = 'asc';
    public const SORT_DSC = 'dsc';
    public const ALLOWED_SORT_ORDER = [self:: SORT_DSC, self::SORT_ASC];

    /**
     * @param array  $data
     * @param string $order
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function sortByDate(array $data, string $order): array;

    /**
     * @param array  $data
     * @param string $order
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    public function sortByScore(array $data, string $order): array;
}
