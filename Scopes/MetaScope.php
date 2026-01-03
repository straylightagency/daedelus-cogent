<?php

namespace Daedelus\Cogent\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 *
 */
class MetaScope implements Scope
{
    /**
     * All the extensions to be added to the builder.
     *
     * @var string[]
     */
    protected array $extensions = [
        'HasMeta', 'HasMetaLike',
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
    public function addHasMeta(Builder $builder): void
    {
        $builder->macro('hasMeta', function (Builder $builder, array|string $meta, mixed $value = null, string $operator = '=') {
            if ( !is_array( $meta ) ) {
                $meta = [ $meta => $value ];
            }

            foreach ( $meta as $key => $value ) {
                $builder->whereHas('meta', function (Builder $builder) use ($key, $value, $operator) {
                    if ( !is_string( $key ) ) {
                        return $builder->where( 'meta_key', $operator, $value );
                    }

                    $builder->where('meta_key', $operator, $key );

                    return is_null( $value ) ? $builder :
                        $builder->where('meta_value', $operator, $value );
                } );
            }

            return $builder;
        } );
    }

    /**
     * @param Builder $builder
     * @return void
     */
    public function addHasMetaLike(Builder $builder): void
    {
        $builder->macro('hasMetaLike', function (Builder $builder, string $meta, mixed $value = null) {
            return $builder->hasMeta( $builder, $meta, $value, 'like' );
        } );
    }
}