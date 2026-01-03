<?php

namespace Daedelus\Cogent;

use Daedelus\Cogent\Concerns\WithMeta;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\CanResetPassword;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property int $ID
 * @property string $user_login
 * @property string $user_pass
 * @property string $user_nicename
 * @property string $user_email
 * @property string $user_url
 * @property string $user_registered
 * @property string $user_activation_key
 * @property string $user_status
 * @property string $display_name
 * @property UserMeta[] $meta
 * @property Post[] $posts
 * @property Comment[] $comments
 */
class User extends Model implements Authenticatable, CanResetPassword
{
    use WithMeta;

    /** @var string */
    const string CREATED_AT = 'user_registered';

    /** @var string|null */
    const ?string UPDATED_AT = null;

    /** @var string */
    protected $table = 'users';

    /** @var string */
    protected $primaryKey = 'ID';

    /** @var array */
    protected $hidden = ['user_pass'];

    /** @var array */
    protected $with = ['meta'];

    /**
     * @return HasMany
     */
    public function posts(): HasMany
    {
        return $this->hasMany(Post::class, 'post_author');
    }

    /**
     * @return HasMany
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class, 'user_id');
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName(): string
    {
        return $this->primaryKey;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return int
     */
    public function getAuthIdentifier(): int
    {
        return $this->attributes[ $this->primaryKey ];
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword(): string
    {
        $authPasswordName = $this->getAuthPasswordName();

        return $this->{$authPasswordName};
    }

    /**
     * Get the name of the password attribute for the user.
     *
     * @return string
     */
    public function getAuthPasswordName(): string
    {
        return 'user_pass';
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken(): ?string
    {
        /** @var UserMeta $meta */
        $meta = $this->meta()->where(
            'meta_key', $this->getRememberTokenName()
        )->first();

        return $meta?->meta_value;
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param string $value
     */
    public function setRememberToken($value): void
    {
        $this->saveMeta( $this->getRememberTokenName(), $value );
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * @return string[]
     */
    public function getCapabilities(): array
    {
        $values = $this->meta()->where(
            'meta_key', 'wp_capabilities'
        )->first()->meta_value;

        return array_keys( array_filter( unserialize( $values ) ) );
    }

    /**
     * @param ...$capabilities
     * @return bool
     */
    public function hasCapabilities(...$capabilities): bool
    {
        $user_capabilities = $this->getCapabilities();

        $capabilities = is_array( $capabilities[0] ) ? $capabilities[0] : $capabilities;

        return array_all( $capabilities, fn ($capability) => in_array( $capability, $user_capabilities, true ) );
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->hasCapabilities( 'administrator' );
    }

    /**
     * Get the e-mail address where password reset links are sent.
     *
     * @return string
     */
    public function getEmailForPasswordReset(): string
    {
        return $this->user_email;
    }

    /**
     * @param string $token
     */
    public function sendPasswordResetNotification($token)
    {
    }

    /**
     * @param mixed $value
     * @return void
     */
    public function setUpdatedAt(mixed $value)
    {
        // Do nothing
    }
}