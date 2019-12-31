<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Dashboard extends Model
{
    public static function total(){
        $user_id = Auth::user()->id;
        $sql = "
            select iwant.total_want, contributes.total_contributes, giving.total_giving,getting.total_getting,want_now.total_want_now,
                want_giving.total_want_giving,want_getting.total_want_getting,want_other.total_want_other from 
                (
                select count(i.id) as total_want from ineed as i
                            join posts as p on (i.post_id=p.id and p.status='active' and p.user_id != $user_id)
                            where i.request_status='active' and i.accept_status='pending' 
                            and i.status=1
                            and i.user_id = $user_id
                ) as iwant,
                (
                select count(p.id) as total_contributes from posts as p
                            where p.give_status is null
                            and p.status='active'
                            and p.user_id = $user_id
                ) as contributes,
                (
                    select count(i.id) as total_giving from ineed as i
                    join posts as p on (p.id=i.post_id and p.status='active' and p.give_status='active' and p.user_id = $user_id)
                    where
                    i.accept_status='active' and i.status=1
                ) as giving,
                (
                    select count(i.id) as total_getting from ineed as i
                    join posts as p on (p.id=i.post_id and p.status='active' and p.give_status='active' and p.user_id != $user_id)
                    where
                    i.accept_status='active' and i.status=1
                    and i.user_id= $user_id
                ) as getting,
                (
                    select count(i.id) as total_want_now from ineed as i
                    join posts as p on (p.id=i.post_id and p.status='active' and p.give_status is null and p.user_id = $user_id)
                    where
                    i.accept_status='pending' and i.status=1
                ) as want_now,
                (
                    select count(i.id) as total_want_giving from ineed as i
                    join posts as p on (p.id=i.post_id and p.status='active' and p.give_status = 'active' and p.user_id = $user_id)
                    where
                    i.accept_status='active' and i.status=1
                ) as want_giving,
                (
                    select count(i.id) as total_want_getting from ineed as i
                    join posts as p on (p.id=i.post_id and p.status='active' and p.give_status = 'active' and p.user_id != $user_id)
                    where
                    i.request_status='active' and i.accept_status='active' and i.status=1
	                 and i.user_id=$user_id		
                ) as want_getting,
                (
                    select count(i.id) as total_want_other from ineed as i
                    join posts as p on (i.post_id=p.id and p.status='active' and p.give_status is null and p.user_id != $user_id)
                    where i.request_status='active' and i.accept_status='pending' 
                    and i.status=1
                    and i.post_id not in (
                        select i.post_id from ineed as i
                        join posts as p on (i.post_id=p.id and p.status='active' and p.give_status is null and p.user_id != $user_id)
                        where i.request_status='active' and i.accept_status='pending' 
                        and i.status=1 and i.user_id= $user_id
                    )	
                ) as want_other
        ";
        $data = DB::select($sql);

        return $data;
    }

    public static function list($type,$page){

        $user_id = Auth::user()->id;

        return Self::$type($page,$user_id);
    }

    public static function iwant($page,$user_id){

        $take = 9;
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

        $take = 9;
        $page = ($page - 1) * $take;

        $sql = "
               select p.id as post_id, p.created_at, p.photo, p.caption, p.status from posts as p
                where p.status = 'active'
                and p.give_status is null
                and p.user_id = $user_id
                order by p.id desc
                limit $page,$take
            ";
        $data = DB::select($sql);
        return $data;
    }

}
