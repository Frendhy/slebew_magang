<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklySchedule extends Model
{
    protected $fillable = [
        'start_date',
        'end_date',
        'morning_shift_team',
        'afternoon_shift_team',
        'night_shift_team'
    ];
}
