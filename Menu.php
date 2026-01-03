<?php

namespace Daedelus\Cogent;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 *
 */
class Menu extends Taxonomy
{
    /** @var string */
    protected string $taxonomy = 'nav_menu';

    /** @var array */
    protected $with = ['term', 'items'];

    /**
     * @return BelongsToMany
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(
            MenuItem::class,
            'term_relationships',
            'term_taxonomy_id',
            'object_id'
        )->orderBy('menu_order');
    }
}