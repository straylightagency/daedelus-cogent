<?php

namespace Daedelus\Cogent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $object_id
 * @property int $term_taxonomy_id
 * @property int $term_order
 */
class TermRelationship extends Model
{
    /** @var string */
    protected $table = 'term_relationships';

    /** @var array */
    protected $primaryKey = ['object_id', 'term_taxonomy_id'];

    /** @var bool */
    public $timestamps = false;

    /** @var bool */
    public $incrementing = false;

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'object_id');
    }

    /**
     * @return BelongsTo
     */
    public function taxonomy(): BelongsTo
    {
        return $this->belongsTo(Taxonomy::class, 'term_taxonomy_id');
    }
}