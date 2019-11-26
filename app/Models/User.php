<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password','avatar','phone','level','user_type','bio','status',
        'pay_name_1','pay_number_1',
        'pay_name_2','pay_number_2',
        'pay_name_3','pay_number_3'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function getJWTIdentifier()
    {
       return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function social()
    {
        return $this->hasMany(UserSocial::class, 'user_id', 'id');
    }

    public function hasSocialLinked($service)
    {
        return (bool) $this->social->where('service', $service)->count();
    }

    public static function updateOverView($input){
        $userId = Auth::user()->id;

        $data['name'] = $input['name'];
        $data['email'] = $input['email'];
        $data['phone'] = $input['phone'];
        $data['bio'] = $input['bio'];
        $data = array_filter($data);

        $update = User::where('id',$userId)
            ->update($data);

        return $update;

    }

    public static function getUser($id){
        $data = User::where('id', $id)->first();

        return $data;
    }
}
