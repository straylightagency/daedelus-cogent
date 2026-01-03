<?php

namespace Daedelus\Cogent;

use Exception;

/**
 * @property int $meta_id
 * @property string $meta_key
 * @property mixed $meta_value
 * @property string $key
 * @property mixed $value
 */
abstract class Meta extends Model
{
    /** @var string */
    protected $primaryKey = 'meta_id';

    /** @var bool */
    public $timestamps = false;

    /**
     * @return string
     */
    public function getKeyAttribute(): string
    {
        return $this->meta_key;
    }

    /**
     * @return mixed
     */
    public function getValueAttribute(): mixed
    {
        try {
            return ( $value = unserialize($this->meta_value) ) === false &&
            $this->meta_value !== false ?
                $this->meta_value :
                $value;
        } catch (Exception $e) {
            return $this->meta_value;
        }
    }
}