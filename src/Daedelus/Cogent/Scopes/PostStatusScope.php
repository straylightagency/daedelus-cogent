<?php

namespace Daedelus\Cogent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 *
 */
class PostStatusScope implements Scope
{
    /**
     * All the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected array $extensions = [
        'WithFuture', 'WithoutFuture', 'OnlyFuture',
        'WithDrafts', 'WithoutDrafts', 'OnlyDrafts',
        'WithPending', 'WithoutPending', 'OnlyPending',
        'WithPrivate', 'WithoutPrivate', 'OnlyPrivate',
        'WithTrashed', 'WithoutTrashed', 'OnlyTrashed',
        'WithoutPublish',
    ];

    /**
     * Apply the scope to a given Eloquent query builder
     *
     * @param Builder $builder
     * @param Model $model
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where( 'post_status', 'publish' );
    }

    /**
     * Extend the query builder with the needed functions.
     *
     * @param Builder $builder
     * @return void
     */
    public function extend(Builder $builder): void
    {
        foreach ( $this->extensions as $extension ) {
            $this->{"add{$extension}"}( $builder );
        }
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithFuture(Builder $builder): void
    {
        $builder->macro('withFuture', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'future' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithoutFuture(Builder $builder): void
    {
        $builder->macro('withoutFuture', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->whereNot('post_status', 'future' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addOnlyFuture(Builder $builder): void
    {
        $builder->macro('onlyFuture', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'future' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithDrafts(Builder $builder): void
    {
        $builder->macro('withDrafts', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'draft' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithoutDrafts(Builder $builder): void
    {
        $builder->macro('withoutDrafts', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->whereNot('post_status', 'draft' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addOnlyDrafts(Builder $builder): void
    {
        $builder->macro('onlyDrafts', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'draft' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithPending(Builder $builder): void
    {
        $builder->macro('withPending', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'pending' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithoutPending(Builder $builder): void
    {
        $builder->macro('withoutPending', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->whereNot('post_status', 'pending' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addOnlyPending(Builder $builder): void
    {
        $builder->macro('onlyPending', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'pending' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithPrivate(Builder $builder): void
    {
        $builder->macro('withPrivate', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'private' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithoutPrivate(Builder $builder): void
    {
        $builder->macro('withoutPrivate', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->whereNot('post_status', 'private' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addOnlyPrivate(Builder $builder): void
    {
        $builder->macro('onlyPrivate', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'private' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithTrashed(Builder $builder): void
    {
        $builder->macro('withTrashed', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                        ->where('post_status', 'trash' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithoutTrashed(Builder $builder): void
    {
        $builder->macro('withoutTrashed', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->whereNot('post_status', 'trash' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addOnlyTrashed(Builder $builder): void
    {
        $builder->macro('onlyTrashed', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->where('post_status', 'trash' );

            return $builder;
        });
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addWithoutPublish(Builder $builder): void
    {
        $builder->macro('withoutPublic', function (Builder $builder) {
            $builder->withoutGlobalScope( $this )
                    ->whereNot('post_status', 'publish' );

            return $builder;
        });
    }
}