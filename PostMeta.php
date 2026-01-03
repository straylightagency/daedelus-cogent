<?php

namespace Daedelus\Cogent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $post_id
 * @property Post $post
 */
class PostMeta extends Meta
{
    /** @var string */
    protected $table = 'postmeta';

    /** @var string[] */
    protected $fillable = ['meta_key', 'meta_value', 'post_id'];

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }
}