<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subuser extends Model
{
    protected $fillable = ['user_id', 'server_id', 'permissions'];

    public function user() { return $this->belongsTo(User::class); }
    public function server() { return $this->belongsTo(Server::class); }

    public function getPermissionList(): array
    {
        return json_decode($this->permissions, true) ?? [];
    }

    public function hasPermission(string $perm): bool
    {
        return in_array($perm, $this->getPermissionList());
    }
}
