<?php

namespace Daedelus\Cogent;

use Daedelus\Support\Filters;
use Illuminate\Database\Eloquent\Builder;

/**
 *
 */
class ThumbnailMeta extends PostMeta
{
    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope('meta_thumbnail', function (Builder $builder) {
            $builder->where('meta_key', '_thumbnail_id');
        } );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'meta_value' => 'int',
        ];
    }

    /**
     * @param string $size
     * @return string
     */
    public function url(string $size = 'thumbnail'): string
    {
        $thumbnail_url = wp_get_attachment_image_url( $this->meta_value, $size );

        return Filters::apply( 'post_thumbnail_url', $thumbnail_url, $this->post, $size );
    }
}