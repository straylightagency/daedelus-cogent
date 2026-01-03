<?php

namespace Daedelus\Cogent;

use Daedelus\Cogent\Concerns\SortableByDate;
use Daedelus\Cogent\Concerns\WithMeta;
use Daedelus\Cogent\Scopes\PostStatusScope;
use Daedelus\Cogent\Scopes\PostTypeScope;
use Daedelus\Support\Filters;
use DateTime;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Arr;
use WP_Post;

/**
 * @property int $ID
 * @property int $post_author
 * @property DateTime $post_date
 * @property DateTime $post_date_gmt
 * @property string $post_content
 * @property string $post_title
 * @property string $post_excerpt
 * @property string $post_status
 * @property string $comment_status
 * @property string $ping_status
 * @property string $post_password
 * @property string $post_name
 * @property string $to_ping
 * @property string $pinged
 * @property DateTime $post_modified
 * @property DateTime $post_modified_gmt
 * @property string $post_content_filtered
 * @property int $post_parent
 * @property string $guid
 * @property int $menu_order
 * @property string $post_type
 * @property string $post_mime_type
 * @property int $comment_count
 * @property string $content
 * @property PostMeta $meta
 * @property User $author
 * @property Attachment $attachment
 * @property Taxonomy[] $taxonomies
 * @property Comment[] $comments
 * @property Category $category
 * @property ThumbnailMeta $thumbnail
 * @property Post $parent
 */
class Post extends Model
{
    use SortableByDate, WithMeta;

    /** @var string */
    const string CREATED_AT = 'post_date';

    /** @var string */
    const string UPDATED_AT = 'post_modified';

    /** @var string */
    protected $table = 'posts';

    /** @var string */
    protected $primaryKey = 'ID';

    /** @var string[] */
    protected $fillable = [
        'post_author',
        'post_date',
        'post_date_gmt',
        'post_content',
        'post_title',
        'post_excerpt',
        'post_status',
        'comment_status',
        'ping_status',
        'post_password',
        'post_name',
        'to_ping',
        'pinged',
        'post_modified',
        'post_modified_gmt',
        'post_content_filtered',
        'post_parent',
        'guid',
        'menu_order',
        'post_type',
        'post_mime_type',
        'comment_count',
    ];

    /** @var string[] */
    protected $with = ['meta'];

    /** @var string */
    public string $postType = 'post';

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::addGlobalScope( new PostStatusScope );
        static::addGlobalScope( new PostTypeScope );
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'post_date' => 'datetime:Y-m-d H:i:s',
            'post_date_gmt' => 'datetime:Y-m-d H:i:s',
            'post_modified' => 'datetime:Y-m-d H:i:s',
            'post_modified_gmt' => 'datetime:Y-m-d H:i:s',
        ];
    }

    /**
     * @param array $attributes
     * @param null $connection
     * @return Post
     */
    public function newFromBuilder($attributes = [], $connection = null): Post
    {
        $attributes = (array) $attributes;

        $model = Model::getPostInstance( $attributes['post_type'] ?? 'post' );

        $model->exists = true;

        $model->setRawAttributes( $attributes, true );

        $model->setConnection(
            $connection ?: $this->getConnectionName()
        );

        $model->fireModelEvent('retrieved', false);

        return $model;
    }

    /**
     * @param string|null $more_link_text
     * @param bool $strip_teaser
     * @return string
     */
    public function content(?string $more_link_text = null, bool $strip_teaser = false):string
    {
        return Filters::apply('the_content', get_the_content( $more_link_text, $strip_teaser, $this->ID ) );
    }

    /**
     * @return string
     */
    public function getContentAttribute():string
    {
        return $this->content();
    }

    /**
     * @return string
     */
    public function getPostType():string
    {
        return $this->postType;
    }

    /**
     * @return HasOne
     */
    public function template(): HasOne
    {
        return $this->hasOne(TemplateMeta::class, 'post_id')
            ->where('meta_key', '_wp_page_template');
    }

    /**
     * @return HasOne
     */
    public function thumbnail(): HasOne
    {
        return $this->hasOne(ThumbnailMeta::class, 'post_id')
                    ->where('meta_key', '_thumbnail_id');
    }

    /**
     * @return HasMany
     */
    public function comments():HasMany
    {
        return $this->hasMany(Comment::class, 'comment_post_ID');
    }

    /**
     * @return BelongsTo
     */
    public function author():BelongsTo
    {
        return $this->belongsTo(User::class, 'post_author');
    }

    /**
     * @return BelongsTo
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_parent')
                    ->withoutGlobalScope( PostTypeScope::class );
    }

    /**
     * @return HasMany
     */
    public function children(): HasMany
    {
        return $this->hasMany(Post::class, 'post_parent')
                    ->withoutGlobalScope( PostTypeScope::class );
    }

    /**
     * @return HasMany
     */
    public function revision(): HasMany
    {
        return $this->hasMany(Post::class, 'post_parent')
                    ->withoutGlobalScope( PostTypeScope::class )
                    ->where('post_type', 'revision');
    }

    /**
     * @return HasMany
     */
    public function attachment(): HasMany
    {
        return $this->hasMany(Attachment::class, 'post_parent')
                    ->withoutGlobalScope( PostTypeScope::class )
                    ->where('post_type', 'attachment');
    }

    /**
     * @return BelongsToMany
     */
    public function taxonomies(): BelongsToMany
    {
        return $this->belongsToMany(
            Taxonomy::class,
            (new TermRelationship())->getTable(),
            'object_id',
            'term_taxonomy_id'
        );
    }

    /**
     * @return BelongsToMany
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(
                Category::class,
                (new TermRelationship())->getTable(),
                'object_id',
                'term_taxonomy_id'
            )->where('taxonomy', 'category');
    }

    /**
     * @return Taxonomy
     */
    public function category(): Taxonomy
    {
        return $this->categories()->first();
    }

    /**
     * Convert a Post model to a WP_Post object
     *
     * @return WP_Post
     */
    public function toBasePost(): WP_Post
    {
        $attributes = $this->only( [
            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content',
            'post_title',
            'post_excerpt',
            'post_status',
            'comment_status',
            'ping_status',
            'post_password',
            'post_name',
            'to_ping',
            'pinged',
            'post_modified',
            'post_modified_gmt',
            'post_content_filtered',
            'post_parent',
            'guid',
            'menu_order',
            'post_type',
            'post_mime_type',
            'comment_count',
        ] );

        return new WP_Post( (object) [ ...$attributes, ...['filter' => 'raw'] ] );
    }

    /**
     * Convert a WP_Post object to a Post model
     *
     * @param WP_Post $wp_post
     * @return self
     */
    public static function fromBasePost(WP_Post $wp_post): self
    {
        return (new self)->newFromBuilder( Arr::only( $wp_post->to_array(), [
            'ID',
            'post_author',
            'post_date',
            'post_date_gmt',
            'post_content',
            'post_title',
            'post_excerpt',
            'post_status',
            'comment_status',
            'ping_status',
            'post_password',
            'post_name',
            'to_ping',
            'pinged',
            'post_modified',
            'post_modified_gmt',
            'post_content_filtered',
            'post_parent',
            'guid',
            'menu_order',
            'post_type',
            'post_mime_type',
            'comment_count',
        ] ) );
    }

    /**
     * @return string
     */
    public function permalink(): string
    {
        return get_permalink( $this->ID );
    }

    /**
     * @return string
     */
    public function thumbnailUrl(): string
    {
        return get_the_post_thumbnail_url( $this->ID );
    }

    /**
     * @param bool $in_same_term
     * @param string $excluded_terms
     * @param string $taxonomy
     * @return ?Post
     */
    public function previous(bool $in_same_term = false, string $excluded_terms = '', string $taxonomy = 'category'): ?Post
    {
        $previous_post = get_previous_post( $in_same_term, $excluded_terms, $taxonomy );

        if ( $previous_post ) {
            return static::fromBasePost( $previous_post );
        }

        return null;
    }

    /**
     * @param bool $in_same_term
     * @param string $excluded_terms
     * @param string $taxonomy
     * @return ?Post
     */
    public function next(bool $in_same_term = false, string $excluded_terms = '', string $taxonomy = 'category'): ?Post
    {
        $next_post = get_next_post( $in_same_term, $excluded_terms, $taxonomy );

        if ( $next_post ) {
            return static::fromBasePost( $next_post );
        }

        return null;
    }

    /**
     * @param string $key
     * @param mixed|null $default
     * @param bool $format_value
     * @return mixed
     */
    public function field(string $key, mixed $default = null, bool $format_value = true): mixed
    {
        if ( ( $value = get_field( $key, $this->ID, $format_value ) ) !== false ) {
            return $value;
        }

        return $default;
    }

    /**
     * @param string|null $key
     * @param mixed|null $default
     * @param bool $format_value
     * @return mixed
     */
    public function fields(?string $key = null, mixed $default = null, bool $format_value = true): mixed
    {
        if ( $key === null ) {
            return get_fields( $this->ID, $format_value );
        }

        return $this->field( $key, $default, $format_value );
    }
}