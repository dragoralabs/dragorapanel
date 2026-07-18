<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $fillable = ['short_code', 'long_name', 'description'];

    public function allocations() { return $this->hasMany(Allocation::class); }
    public function nodes() { return $this->hasMany(Node::class); }
}
