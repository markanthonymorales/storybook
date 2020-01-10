<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Facades\Hash;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'firstname', 'lastname', 'middlename', 'nickname', 'gender', 'birthdate', 'email', 'password', 'status', 'activation_code',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role(){
        return $this->belongsTo('App\Role','role_id');
    }

    public function story(){
        return $this->hasMany('App\Story')->orderBy('id', 'DESC');
    }

    public function books(){
        return $this->hasMany('App\Book')->orderBy('id', 'DESC');
    }

    public function address(){
        return $this->hasMany('App\Address')->orderBy('id', 'DESC');
    }

    public function carts(){
        return $this->hasMany(config('cart.cart_model'), 'user_id')->completed();
    }

    public function getName()
    {
        return $this->firstname.' '.$this->lastname;
    }

    public function scopeActive($query)
    {
        return $query->where('is_deleted', 0);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_deleted', 1);
    }

    /**
     * Find the user instance for the given username.
     *
     * @param  string  $username
     * @return \App\User
     */
    public function findForPassport($username)
    {
        return $this->where('email', $username)->first();
    }

    /**
    * Validate the password of the user for the Passport password grant.
    *
    * @param  string $password
    * @return bool
    */
    public function validateForPassportPasswordGrant($password)
    {
        return Hash::check($password, $this->password);
    }
}
