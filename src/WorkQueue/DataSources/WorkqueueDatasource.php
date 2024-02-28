<?php

namespace App\WorkQueue\DataSources;

use App\WorkQueue\ColumnCollection;

interface WorkqueueDatasource
{
    public function getWorkqueueData(int $offset, int $limit, ColumnCollection $columnCollection): array;
    public function hasMoreResults(): bool;
    public function lastNumResults(): int;
    //Todo: Remove before production merge.
    public function rawQuery($query);
}
