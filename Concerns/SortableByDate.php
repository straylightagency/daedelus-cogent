<?php

namespace Daedelus\Cogent\Concerns;

use Illuminate\Database\Eloquent\Builder;

/**
 * Make a model sortable by date.
 */
trait SortableByDate
{
    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeNewest(Builder $query): Builder
    {
        return $query->orderBy(static::CREATED_AT, 'desc');
    }

    /**
     * @param Builder $query
     * @return Builder
     */
    public function scopeOldest(Builder $query): Builder
    {
        return $query->orderBy(static::CREATED_AT, 'asc');
    }
}