<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{






    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'provider',
        'provider_id',
        'avatar',
        'plan_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class)
            ->withPivot('role')
            ->withTimestamps();
    }


    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function joinRequests()
    {
        return $this->hasMany(TeamJoinRequest::class);
    }



    public function adminTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->wherePivot('role', 'admin');
    }


    // الفرق اللي هو member فيها فقط
    public function joinedTeams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class)
            ->wherePivot('role', 'member');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    // دالة للتحقق: هل المستخدم يقدر ينشئ فريق جديد؟
    public function canCreateTeam()
    {
        // لو الخطة unlimited (قيمة -1)
        if ($this->plan->max_created_teams === -1) {
            return true;
        }

        // عدد الفرق اللي أنشأها المستخدم
        // $createdTeamsCount = $this->ownedTeams()->count(); // أو teams() حسب الـ relation عندك
        $createdTeamsCount = $this->adminTeams()->count(); // أو teams() حسب الـ relation عندك

        return $createdTeamsCount < $this->plan->max_created_teams;
    }

    // دالة للتحقق: هل المستخدم يقدر ينضم لفريق جديد؟
    public function canJoinTeam()
    {
        if ($this->plan->max_joined_teams === -1) {
            return true;
        }

        $joinedTeamsCount = $this->teams()->count(); // الفرق اللي منضم فيها

        return $joinedTeamsCount < $this->plan->max_joined_teams;
    }


    // Chat functions
    public function chats()
    {
        return $this->belongsToMany(Chat::class, 'chat_user');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
