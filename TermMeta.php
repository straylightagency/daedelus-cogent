<?php

namespace Daedelus\Cogent;

use Daedelus\Cogent\Concerns\WithMeta;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $term_id
 */
class TermMeta extends Meta
{
    use WithMeta;

    /** @var string */
    protected $table = 'termmeta';

    /** @var array */
    protected $fillable = ['meta_key', 'meta_value', 'term_id'];

    /**
     * @return BelongsTo
     */
    public function term(): BelongsTo
    {
        return $this->belongsTo(Term::class);
    }
}