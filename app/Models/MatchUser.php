<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatchUser extends Model
{
    use HasFactory;

    protected $table = 'match_users';

    protected $fillable = [
        'user_id',
        'matched_user_id',
        'score',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function matchedUser()
    {
        return $this->belongsTo(User::class, 'matched_user_id');
    }
}
