<?php

namespace Daedelus\Cogent;

use Daedelus\Cogent\Concerns\WithMeta;
use Illuminate\Database\Eloquent\Relations\HasOne;
use WP_Term;

/**
 * @property int $term_id
 * @property string $name
 * @property string $slug
 * @property int $term_group
 * @property int $term_order
 * @property TermMeta $meta
 * @property Taxonomy $taxonomy
 */
class Term extends Model
{
    use WithMeta;

    /** @var string */
    protected $table = 'terms';

    /** @var string */
    protected $primaryKey = 'term_id';

    protected $fillable = [
        'name',
        'slug',
        'term_group',
        'term_order',
    ];

    /** @var bool */
    public $timestamps = false;

    /**
     * @return HasOne
     */
    public function taxonomy(): HasOne
    {
        return $this->hasOne(Taxonomy::class, 'term_id');
    }

    /**
     * Convert a Term model to a WP_Term object
     *
     * @return WP_Term
     */
    public function toBaseTerm(): WP_Term
    {
        $attributes = $this->only( [
            'term_id',
            'name',
            'slug',
            'term_group',
            'term_order',
        ] );

        return new WP_Term( (object) [ ...$attributes, ...['filter' => 'raw'] ] );
    }

    /**
     * Convert a WP_Term object to a Term model
     *
     * @param WP_Term $wp_term
     * @return static
     */
    public static function fromBaseTerm(WP_Term $wp_term): static
    {
        $term = new static();

        $term->term_id = $wp_term->term_id;
        $term->name = $wp_term->name;
        $term->slug = $wp_term->slug;
        $term->term_group = $wp_term->term_group;
        $term->term_order = $wp_term->term_order ?? 0;

        return $term;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @param bool $format_value
     * @param bool $escape_html
     * @return mixed
     */
    public function field(string $key, mixed $default = null, bool $format_value = true, bool $escape_html = false): mixed
    {
        if ( ( $value = get_field( $key, $this->term_id, $format_value, $escape_html ) ) !== false ) {
            return $value;
        }

        return $default;
    }
}