<?php

namespace Wbe\Crud\Models\ContentTypes;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function get($id)
    {
        return \DB::table('users')->where('id', $id)->first();
    }

    public function news()
    {
        return $this->hasMany('App\Models\ContentTypes\News', 'user_id', 'id');
    }


    public function posts()
    {
        return $this->hasOne('App\Models\ContentTypes\Posts', 'id', 'id');
    }
}