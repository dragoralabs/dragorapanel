<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'email', 'password', 'first_name', 'last_name', 'role',
        'email_verified_at', 'language', 'timezone',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }

    public function servers() { return $this->hasMany(Server::class); }
    public function sessions() { return $this->hasMany(Session::class); }
    public function subusers() { return $this->hasMany(Subuser::class); }
    public function apiTokens() { return $this->hasMany(ApiToken::class); }
    public function notifications() { return $this->hasMany(Notification::class); }
    public function activityLogs() { return $this->hasMany(ActivityLog::class); }

    public function isAdmin(): bool { return $this->role === 'admin'; }
}
