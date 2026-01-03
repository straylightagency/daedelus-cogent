<?php

namespace Daedelus\Cogent;

use Illuminate\Database\Eloquent\Model as BaseModel;
use Illuminate\Support\Str;

/**
 *
 */
abstract class Model extends BaseModel
{
    /** @var array  */
    protected static array $postTypes = [];

    /** @var array  */
    protected static array $taxonomyTypes = [];

    /**
     * Get the table associated with the model.
     *
     * @return string
     */
    public function getTable(): string
    {
        global $wpdb;

        return Str::start( parent::getTable(), $wpdb->prefix );
    }

    /**
     * @param string $class_name
     * @return void
     */
    public static function registerPostType(string $class_name): void
    {
        static::$postTypes[ ( new $class_name )->postType ] = $class_name;
    }

    /**
     * @param string $class_name
     * @return void
     */
    public static function registerTaxonomy(string $class_name): void
    {
        static::$taxonomyTypes[ ( new $class_name )->taxonomy ] = $class_name;
    }

    /**
     * @param string $post_type
     * @return Post
     */
    public static function getPostInstance(string $post_type): Post
    {
        $class = static::$postTypes[ $post_type ] ?? Post::class;

        return new $class();
    }

    /**
     * @param string $taxonomy_type
     * @return Taxonomy
     */
    public static function getTaxonomyInstance(string $taxonomy_type): Taxonomy
    {
        $class = static::$taxonomyTypes[ $taxonomy_type ] ?? Taxonomy::class;

        return new $class();
    }
}