<?php

namespace Wbe\Crud\Models\ContentTypes;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Notifications\Messages\MailMessage;

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

    public function role()
    {
        return $this->hasOne('Wbe\Crud\Models\Roles\Role', 'id', 'role_id');
    }

    public static function get($id)
    {
        return \DB::table('users')->where('id', $id)->first();
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new CustomPassword($token));
    }
}

class CustomPassword extends ResetPassword
{
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->line('We are sending this email because we recieved a forgot password request.')
            ->action('Reset Password', url(route('password.token', $this->token, false)))
            ->line('If you did not request a password reset, no further action is required. Please contact us if you did not submit this request.');
    }
}
