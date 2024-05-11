<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        "full_name",
        "username",
        "bio",
        "is_private",
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
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

    public function acceptsFollowFrom($user_id){
        $follow = Follow::where('follower_id', $user_id)->where('following_id', $this->id)->first();
        return $follow && $follow->is_accepted;
    }

    public function posts(){
        return $this->hasMany(Post::class, 'post_id');
    }

    public function following_users(){
        return $this->belongsToMany(User::class, 'follows', 'follower_id', 'following_id', 'id', 'id');
    }

    public function followers(){
        return $this->belongsToMany(User::class, 'follows', 'following_id', 'follower_id', 'id', 'id');
    }
}
