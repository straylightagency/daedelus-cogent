<?php

namespace Daedelus\Cogent;

use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class TemplateMeta extends PostMeta
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('meta_template', function (Builder $builder) {
            $builder->where('meta_key', '_wp_page_template');
        } );
    }

    /**
     * @return mixed|string
     */
    public function __toString()
    {
        return $this->value;
    }
}