<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Team extends Model
{

    use HasFactory;
    //
    protected $fillable = [
        'name',
        'description',
        "owner_id",
        "join_code"
    ];

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }


    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('role')
            ->withTimestamps();
    }

    public function joinRequests()
    {
        return $this->hasMany(TeamJoinRequest::class);
    }

    public function hasUser($userId): bool
    {
        return $this->users()
            ->where('user_id', $userId)
            ->exists();
    }

    public function isAdmin($userId)
    {
        return $this->users()
            ->where('user_id', $userId)
            ->wherePivot('role', 'admin')
            ->exists();
    }

    public function admins(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->wherePivot('role', 'admin');
    }

    public function admin()
    {
        return $this->belongsTo(User::class)->wherePivot('role', 'admin');
    }

    // Team Chats
    public function chats()
    {
        return $this->hasMany(Chat::class);
    }
}
