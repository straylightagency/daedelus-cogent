<?php

namespace Daedelus\Cogent;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $term_taxonomy_id
 * @property int $term_id
 * @property string $taxonomy
 * @property string $description
 * @property int $parent
 * @property string $count
 * @property TermMeta $meta
 * @property Term $term
 * @property string $name
 * @property string $slug
 * @property int $group
 * @property int $order
 */
class Taxonomy extends Model
{
    /** @var string */
    protected $table = 'term_taxonomy';

    /** @var string */
    protected $primaryKey = 'term_taxonomy_id';

    /** @var array */
    protected $with = ['term'];

    /** @var bool */
    public $timestamps = false;

    /**
     * @param array $attributes
     * @param null $connection
     * @return Taxonomy
     */
    public function newFromBuilder($attributes = [], $connection = null): Taxonomy
    {
        $attributes = (array) $attributes;

        $model = Model::getTaxonomyInstance( $attributes['post_type'] ?? 'post' );

        $model->exists = true;

        $model->setRawAttributes( $attributes, true );

        $model->setConnection(
            $connection ?: $this->getConnectionName()
        );

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    /**
     * @return HasMany
     */
    public function meta(): HasMany
    {
        return $this->hasMany(TermMeta::class, 'term_id');
    }

    /**
     * @return BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class, 'term_id');
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'parent');
    }

    /**
     * @return BelongsToMany
     */
    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(
            Post::class,
            (new TermRelationship())->getTable(),
            'term_taxonomy_id',
            'object_id'
        );
    }

    /**
     * @return string
     */
    public function getNameAttribute():string
    {
        return $this->term->name;
    }

    /**
     * @return string
     */
    public function getSlugAttribute():string
    {
        return $this->term->slug;
    }

    /**
     * @return int
     */
    public function getGroupAttribute():int
    {
        return $this->term->term_group;
    }

    /**
     * @return int
     */
    public function getOrderAttribute():int
    {
        return $this->term->term_order;
    }
}