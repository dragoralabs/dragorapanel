<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScheduleTask extends Model
{
    protected $fillable = ['schedule_id', 'sequence_id', 'action', 'payload', 'time_offset', 'is_queued'];

    public function schedule() { return $this->belongsTo(Schedule::class); }
}
