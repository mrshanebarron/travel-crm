<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'whatsapp_opt_in_sent_at',
        'password',
        'role',
    ];

    /**
     * Check if user is a super admin (using Spatie roles)
     */
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    /**
     * Check if user is an admin or higher (using Spatie roles)
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

    /**
     * Check if user is any staff member (has any role)
     */
    public function isStaff(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin', 'user']);
    }

    /**
     * Check if user can manage (same as admin for now, but could be different)
     */
    public function isManager(): bool
    {
        return $this->hasAnyRole(['super_admin', 'admin']);
    }

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
}
