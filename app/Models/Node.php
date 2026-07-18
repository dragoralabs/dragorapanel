<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    protected $fillable = [
        'name', 'fqdn', 'ip_address', 'port', 'location_id', 'token',
        'memory_mb', 'storage_mb', 'cpu_cores',
        'disk_used_mb', 'memory_used_mb', 'cpu_percent',
        'status', 'last_seen_at',
    ];

    protected $casts = [
        'last_seen_at' => 'datetime',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class);
    }

    public function servers()
    {
        return $this->hasMany(Server::class);
    }

    public function allocations()
    {
        return $this->hasMany(Allocation::class);
    }

    public function isOnline(): bool
    {
        return $this->status === 'online';
    }
}
