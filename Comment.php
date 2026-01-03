<?php

namespace Daedelus\Cogent;

use Daedelus\Cogent\Concerns\SortableByDate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 *
 */
class Comment extends Model
{
    use SortableByDate;

    /** @var string */
    const string CREATED_AT = 'comment_date';

    /** @var null */
    const null UPDATED_AT = null;

    /** @var string */
    protected $table = 'comments';

    /** @var string */
    protected $primaryKey = 'comment_ID';

    /**
     * @return BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'comment_post_ID');
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_parent');
    }

    /**
     * @return HasMany
     */
    public function replies(): HasMany
    {
        return $this->hasMany(Comment::class, 'comment_parent');
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setUpdatedAt(mixed $value)
    {
        // Do nothing
    }
}