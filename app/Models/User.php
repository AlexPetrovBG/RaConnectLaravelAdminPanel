<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'full_name',
        'is_active',
        'schedule_nick',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function manDays(): HasMany
    {
        return $this->hasMany(ManDay::class);
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class, 'from_user_id');
    }

    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'to_user_id');
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class);
    }

    // Business logic methods
    public function hasRight(string $right): bool
    {
        return $this->hasPermissionTo($right);
    }

    public function hasRole(string $role): bool
    {
        return $this->hasRole($role);
    }
}


