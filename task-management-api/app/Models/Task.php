<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


class Task extends Model
{

    use HasFactory;
    protected $fillable = [
        'user_id',
        'team_id',
        'title',
        'description',
        'status',
        // 'assigned_to',
        'created_by',
        'assigned_to',
        'end_date'
    ];

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    // public function assignee()
    // {
    //     return $this->belongsTo(User::class, 'assigned_to');
    // }

    public function assigned_to()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    protected $casts = [
        'end_date' => 'date',
    ];

    protected $appends = [
        'end_date_formatted',
        'end_date_relative',
        'deadline_status',
        'days_left'
    ];

    public function getEndDateFormattedAttribute()
    {
        return $this->end_date?->format('M d'); // Oct 24
    }

    public function getEndDateRelativeAttribute()
    {
        return $this->end_date?->diffForHumans(); // in 3 days
    }


    public function getDaysLeftAttribute()
    {
        return $this->end_date
            ? Carbon::now()->diffInDays($this->end_date, false)
            : null;
    }

    public function getDeadlineStatusAttribute()
    {
        if (!$this->end_date) return 'none';

        $days = $this->days_left;

        if ($days < 0) return 'expired';
        if ($days <= 1) return 'danger';
        if ($days <= 3) return 'warning';

        return 'safe';
    }
}
