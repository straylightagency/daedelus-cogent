<?php

namespace Daedelus\Cogent;

use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $user_id
 */
class UserMeta extends Meta
{
    /** @var string */
    protected $table = 'usermeta';

    /** @var string */
    protected $primaryKey = 'umeta_id';

    /** @var array */
    protected $fillable = [
        'meta_key',
        'meta_value',
        'user_id'
    ];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}