<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
        'server_id', 'name',
        'cron_minute', 'cron_hour', 'cron_day_of_week', 'cron_day_of_month',
        'is_active', 'last_run_at',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'last_run_at' => 'datetime'];
    }

    public function server() { return $this->belongsTo(Server::class); }
    public function tasks() { return $this->hasMany(ScheduleTask::class)->orderBy('sequence_id'); }

    public function isDue(): bool
    {
        // rough check: if last run was more than the cron interval ago
        if (!$this->last_run_at) return true;
        return $this->last_run_at->diffInMinutes(now()) >= 1;
    }
}
