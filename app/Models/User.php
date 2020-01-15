<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
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

    public static function changeAvatar($input){
        try{
            $userId = Auth::user()->id;

            $base64_image = $input['avatar'];
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                $data_img = substr($base64_image, strpos($base64_image, ',') + 1);

                $user = User::getUser($userId);
                $avatar_name = $user->avatar;

                $data_img = base64_decode($data_img);
                Storage::disk('avatar')->put($avatar_name, $data_img);
                if (!Storage::disk('avatar')->exists($avatar_name)) {
                    throw new \Exception('Cannot change avatar!');
                }

                $data['avatar'] = $avatar_name;

                $update = User::where('id',$userId)
                    ->update($data);
            }

            $msg['status'] = true;

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage().$e->getLine();
            $msg['status'] = false;
        }

        return $msg;

    }

    public static function getUser($id){
        $data = User::where('id', $id)->first();

        return $data;
    }

    public static function userByTopViewLIst($input){
        $sql = "
            select count(ua.object_id) as num_view,u.avatar, u.name,u.id from user_activity as ua
            join users as u on u.id=ua.object_id
            where 
            ua.object_type='profile'
            group by ua.object_id, u.avatar, u.name,u.id
           -- HAVING count(ua.object_id) > 1
            order by num_view desc
            limit 10
            ";
        $data = DB::select($sql);

        return $data;
    }

    public static function userByTopContributionLIst($input){
        $sql = "
            select count(p.user_id) as num_post, u.avatar, u.id, u.name from users as u
            left join posts as p on (p.user_id=u.id and p.status='active')
            where 
            u.status='active'
            group by u.avatar, u.id, u.name
            order by num_post desc
            limit 5
            ";
        $data = DB::select($sql);

        return $data;
    }

    public static function listPeople($input){
        $search = null;
        if (isset($input['search'])) {
            $search = " and LOWER(u.name) LIKE '%" . strtolower($input['search']) . "%' ";
        }

        $take = 9;
        $page = ($input['page'] - 1) * $take;

        $sql = "
            select count(p.user_id) as num_post, u.avatar, u.id, u.name from users as u
            left join posts as p on (p.user_id=u.id and p.status='active')
            where 
            u.status='active'
            $search
            group by u.avatar, u.id, u.name
            order by num_post desc
            limit $page,$take
            ";
        $data = DB::select($sql);

        return $data;
    }

    public static function listPeopleWant($input){
        $user_id = $input['id'];

        $take = 15;
        $page = ($input['page'] - 1) * $take;

        $sql_other = "
                select 
                    p.caption,p.created_at as post_date,p.id as post_id,p.photo,
                    p.give_status,
                    u.id as user_id,u.avatar as user_avatar,u.name as user_name,
                    i.created_at as want_date, i.accept_status, i.request_status,
                    i.accept_date, i.request_date,
                    'other-want' as type
                from ineed as i
                join posts as p on (p.id=i.post_id and p.status='active' and p.user_id=$user_id)
                join users as u on (u.id=i.user_id and u.status='active')
                where
                i.user_id!=$user_id
                and i.status=1
                order by i.id desc
                limit $page,$take
            ";

        $sql_him = "
                select 
                p.caption,p.created_at as post_date,p.id as post_id,p.photo,
                u.id as user_id,u.avatar as user_avatar,u.name as user_name,
                i.created_at as want_date, i.accept_status, i.request_status,
                i.accept_date, i.request_date,
                'he-want' as type
                from ineed as i
                join posts as p on (p.id=i.post_id and p.status='active')
                join users as u on (u.id=i.user_id and u.status='active')
                where
                i.user_id=$user_id
                and i.status=1
                order by i.id desc
                limit $page,$take
             ";

        $sql = null;
        if($input['filter_type'] == 'other-want'){
            $sql = $sql_other;
        }elseif($input['filter_type'] == 'he-want'){
             $sql = $sql_him;
        }else{
            $sql = $sql_other." UNION ".$sql_him;
        }

        $sqlFinal = "
            select * from 
                (
                    $sql
                ) as people_want order by want_date DESC
                limit $page,$take
        ";

        $data = DB::select($sqlFinal);

        return $data;
    }

}
