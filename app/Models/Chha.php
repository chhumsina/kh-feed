<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB; 

class Chha extends Model
{

    public static function filterPost($input)
    {

        $user_id = Auth::user()->id;

        $caption = null;
        if (!is_null($input['caption'])) {
            $caption = " and LOWER(p.caption) LIKE '%" . strtolower($input['caption']) . "%' ";
        }

        $status = $input['status'];
        $status = " p.status = '$status'";
        
        if($input['giveStatus'] == 'not-yet-give'){
            $giveStatus = " and p.give_status is null";
        }else{
            $giveStatus = $input['giveStatus'];
            $giveStatus = " and p.give_status = '$giveStatus'";
        }
        
        $sortBy = $input['shortBy'];
        $orderBy = $input['orderBy'];
        $orderBy = " p.$orderBy ";
        $numPage = $input['numPage'];

        $sql = "
            select p.id as post_id,p.give_status,p.give_date, p.created_at,p.updated_at, p.photo, p.caption, p.status, u.id as user_id, u.avatar, u.name from posts as p
            join users as u on u.id=p.user_id
            where  
            $status
            $giveStatus
            $caption 
            order by $orderBy $sortBy
            limit $numPage
        "; 
        $data = DB::select($sql);

        return $data;

    }

    public static function postChangeStatus($input){
        try {
 
            $id = $input->post_id;
            $status = $input->status;
            if($status == 'pending'){
                $status = 'active';
            }else{
                $status = 'pending';
            }

            $update = Posts::where('id', $id)->update(['status'=>$status]); 
            $msg['status'] = true;

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }

        return $msg;
    }

    public static function isChha(){
        $obj = Auth::user();
        $user_level = $obj->level;
        $user_email = $obj->email;
        $user_id = $obj->id;

        if($user_level == 111 && $user_email == 'chhumsina@gmail.com' && in_array($user_id,[1,2])){
            return true;
        }else{
            return false;
        }

    }
}
