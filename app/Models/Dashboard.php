<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Model
{
    public static function list($type,$page){

        $user_id = Auth::user()->id;

        return Self::$type($page,$user_id);
    }

    public static function iwant($page,$user_id){

        $take = 1;
        $page = ($page - 1) * $take;

        $sql = "
               select i.*, u.avatar, u.name, u.id as user_id, p.id as post_id, p.caption, p.photo, p.created_at as post_date from ineed as i
                join users as u on (i.user_id=u.id and u.status='active')
                join posts as p on (i.post_id=p.id and p.status='active')
                where 
                i.user_id = $user_id
                order by i.created_at desc
                limit $page,$take
            ";
        $data = DB::select($sql);

        return $data;
    }

    public static function contributes($page,$user_id){

        $take = 1;
        $page = ($page - 1) * $take;

        $sql = "
               select i.*, u.avatar, u.name, u.id as user_id, p.id as post_id, p.caption, p.photo, p.created_at as post_date from ineed as i
                join users as u on (i.user_id=u.id and u.status='active')
                join posts as p on (i.post_id=p.id and p.status='active')
                where 
                i.user_id = $user_id
                order by i.created_at desc
                limit $page,$take
            ";
        $data = DB::select($sql);

        return $data;
    }

}
