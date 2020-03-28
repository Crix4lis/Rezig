<?php

declare(strict_types=1);

namespace Tests\Rezig\data;

class ImmutableDateTimeMotherObject
{
    public static function createFirst(): \DateTimeImmutable
    {
        return new \DateTimeImmutable("2020-03-28T00:09:42+0000");
    }

    public static function createFirstAsString(): string
    {
        return "2020-02-28T00:09:42+0000";
    }

    public static function createSecondAsString(): string
    {
        return "2020-03-28T00:09:42+0000";
    }

    public static function createLastAsString(): string
    {
        return "2020-04-28T00:09:42+0000";
    }
}
