<?php

namespace Daedelus\Cogent\Concerns;

use Daedelus\Cogent\Comment;
use Daedelus\Cogent\CommentMeta;
use Daedelus\Cogent\Post;
use Daedelus\Cogent\PostMeta;
use Daedelus\Cogent\Scopes\MetaScope;
use Daedelus\Cogent\Term;
use Daedelus\Cogent\TermMeta;
use Daedelus\Cogent\User;
use Daedelus\Cogent\UserMeta;
use Illuminate\Database\Eloquent\Relations\HasMany;
use UnexpectedValueException;

/**
 *
 */
trait WithMeta
{
    /**
     * @var array
     */
    protected array $builtInClasses = [
        Comment::class => CommentMeta::class,
        Post::class => PostMeta::class,
        Term::class => TermMeta::class,
        User::class => UserMeta::class,
    ];

    /**
     * Boot the soft deleting trait for a model.
     *
     * @return void
     */
    public static function bootWithMeta(): void
    {
        static::addGlobalScope( new MetaScope );
    }

    /**
     * Get the right meta class based on the model
     *
     * @return string
     * @throws UnexpectedValueException
     */
    protected function getMetaClass(): string
    {
        foreach ( $this->builtInClasses as $model => $meta ) {
            if ( $this instanceof $model ) {
                return $meta;
            }
        }

        throw new UnexpectedValueException( sprintf(
            '%s must extends one of built-in models: Comment, Post, Term or User.',
            static::class
        ) );
    }

    /**
     * Get the right foreign key based on the model
     *
     * @return string
     * @throws UnexpectedValueException
     */
    protected function getMetaForeignKey(): string
    {
        foreach ( $this->builtInClasses as $model => $meta ) {
            if ( $this instanceof $model) {
                return sprintf('%s_id', strtolower( class_basename( $model ) ) );
            }
        }

        throw new UnexpectedValueException( sprintf(
            '%s must extends one of built-in models: Comment, Post, Term or User.',
            static::class
        ) );
    }

    /**
     * Return the meta relationship from that model
     *
     * @return HasMany
     */
    public function meta(): HasMany
    {
        return $this->hasMany( $this->getMetaClass(), $this->getMetaForeignKey() );
    }

    /**
     * @return $this
     */
    public function saveMeta(string $key, mixed $value): static
    {
        $class = $this->getMetaClass();

        $this->meta()->save(
            new $class([
                'meta_key' => $key,
                'meta_value' => $value,
            ])
        );

        return $this;
    }
}