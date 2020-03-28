<?php

declare(strict_types=1);

namespace App\Rezig\Scores\DataProvider\Exception;

class ResourceNotFound extends \RuntimeException
{
    public function __construct(string $resourceName, string $resourceId)
    {
        parent::__construct(sprintf('Resource %s not found by id: %s', $resourceName, $resourceId));
    }
}
