<?php

namespace Daedelus\Cogent;

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $option_id
 * @property string $option_name
 * @property mixed $option_value
 * @property mixed $value
 * @property string $autoload
 */
class Option extends Model
{
    /** @var string */
    protected $table = 'options';

    /** @var string  */
    protected $primaryKey = 'option_id';

    /** @var bool */
    public $timestamps = false;

    /** @var array */
    protected $fillable = [
        'option_name',
        'option_value',
        'autoload',
    ];

    /**
     * @return mixed
     */
    public function getValueAttribute(): mixed
    {
        try {
            return ( $value = unserialize( $this->option_value ) ) === false &&
                    $this->option_value !== false ?
                $this->option_value :
                $value;
        } catch ( Exception $e ) {
            return $this->option_value;
        }
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return Option
     */
    public static function add(string $key, mixed $value): Option
    {
        return static::query()->create( [
            'option_name' => $key,
            'option_value' => is_array( $value ) ? serialize( $value ) : $value,
        ] );
    }

    /**
     * @param string $name
     * @return mixed
     */
    public static function get(string $name): mixed
    {
        if ( $option = self::query()->where('option_name', $name)->first() ) {
            return $option->value;
        }

        return null;
    }

    /**
     * @param array $keys
     * @return array
     */
    public static function asArray(array $keys = [] ): array
    {
        $query = static::query();

        if ( !empty( $keys ) ) {
            $query->whereIn('option_name', $keys);
        }

        return $query->get()
                    ->pluck('value', 'option_name')
                    ->toArray();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [$this->option_name => $this->value];
    }
}