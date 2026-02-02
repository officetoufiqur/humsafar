<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'display_name',
        'fname',
        'lname',
        'dob',
        'photo',
        'email',
        'password',
        'otp',
        'is_accept',
        'is_complete',
        'is_permission',
        'is_online',
        'language',
        'members_with_photo',
        'vip_members',
        'blur_photo',
        'members_send_request',
        'status',
        'membership_type',
        'vip_expires_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    public function profile()
    {
        return $this->hasOne(Profile::class);
    }

    public function lookingFor()
    {
        return $this->hasOne(LookingFor::class);
    }

    public function profileVisits()
    {
        return $this->hasMany(ProfileVisit::class);
    }

    public function blockedUsers()
    {
        return $this->belongsToMany(
            User::class,
            'blocks',
            'blocker_id',
            'blocked_id'
        );
    }

    public function blockedBy()
    {
        return $this->belongsToMany(
            User::class,
            'blocks',
            'blocked_id',
            'blocker_id'
        );
    }

    public function isOnline()
    {
        return Cache::has('user-is-online-'.$this->id);
    }

    public function latestMessage()
    {
        return $this->hasOne(Message::class, 'sender_id')
            ->orWhere('receiver_id', $this->id)
            ->latest();
    }
}
