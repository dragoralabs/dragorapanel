<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = ['user_id', 'type', 'title', 'body', 'read_at'];

    protected function casts(): array
    {
        return ['read_at' => 'datetime'];
    }

    public function user() { return $this->belongsTo(User::class); }

    public function isRead(): bool { return $this->read_at !== null; }
    public function scopeUnread($q) { return $q->whereNull('read_at'); }
}
