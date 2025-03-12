<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'lockout_time',
        'failed_attempts',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'lockout_time' => 'datetime',
            'failed_attempts' => 'integer',
        ];
    }

    /**
     * Handle account lockout mechanism.
     *
     * @return bool
     */
    public function isLockedOut(): bool
    {
        if ($this->lockout_time && $this->lockout_time->isFuture()) {
            return true;
        }

        return false;
    }

    /**
     * Increment failed login attempts.
     *
     * @return void
     */
    public function incrementFailedAttempts(): void
    {
        $this->failed_attempts++;
        $this->save();
    }

    /**
     * Reset failed login attempts.
     *
     * @return void
     */
    public function resetFailedAttempts(): void
    {
        $this->failed_attempts = 0;
        $this->save();
    }

    /**
     * Lock the account for a specified duration.
     *
     * @param int $minutes
     * @return void
     */
    public function lockout(int $minutes): void
    {
        $this->lockout_time = now()->addMinutes($minutes);
        $this->save();
    }

    /**
     * Filter users by email.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $email
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByEmail($query, $email)
    {
        if ($email) {
            return $query->where('email', 'like', "%{$email}%");
        }

        return $query;
    }

    /**
     * Filter users by name.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $name
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByName($query, $name)
    {
        if ($name) {
            return $query->where('name', 'like', "%{$name}%");
        }

        return $query;
    }

    /**
     * Filter users by lockout status.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|null $lockoutStatus
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFilterByLockoutStatus($query, $lockoutStatus)
    {
        if ($lockoutStatus) {
            return $query->where('lockout_status', $lockoutStatus);
        }

        return $query;
    }

    /**
     * Paginate the user list.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function scopePaginateUsers($query, $perPage = 10)
    {
        return $query->paginate($perPage);
    }
}
