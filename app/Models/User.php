<?php

namespace App\Models;

use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use  Notifiable, HasRoles;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    // use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
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
        ];
    }

    /**
     * Get tasks assigned to the user
     */
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to');
    }

    public function completedTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'assigned_to')->whereNotNull('completed_at');
    }


    /**
     * Get tasks created by the user
     */
    public function createdTasks(): HasMany
    {
        return $this->hasMany(Task::class, 'created_by');
    }

    /**
     * Get projects managed by the user
     */
    public function projects(): HasMany
    {
        return $this->hasMany(Project::class, 'manager_id');
    }

    /**
     * Get total tasks count (assigned + created)
     */
    public function totalTasks()
    {
        if ($this->hasRole('admin')) {
            return Task::query();
        }

        return Task::where('assigned_to', $this->id)
            ->orWhere('created_by', $this->id);
    }
}
