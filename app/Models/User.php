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
}
