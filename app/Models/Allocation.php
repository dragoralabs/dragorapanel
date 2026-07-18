<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    protected $fillable = ['node_id', 'location_id', 'ip', 'port', 'server_id', 'assigned'];

    public function location() { return $this->belongsTo(Location::class); }
    public function node() { return $this->belongsTo(Node::class); }
    public function server() { return $this->belongsTo(Server::class); }
}
