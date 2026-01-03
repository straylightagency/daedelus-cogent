<?php

namespace Daedelus\Cogent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $comment_id
 */
class CommentMeta extends Meta
{
    protected $table = 'commentmeta';

    /** @var array */
    protected $fillable = ['meta_key', 'meta_value', 'comment_id'];

    /**
     * @return BelongsTo
     */
    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}