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

    public static function postDelete($input){
        try {
 
            $id = $input->post_id;
            $status = $input->status;
            if($status == 'pending'){
                $status = 'active';
            }else{
                $status = 'pending';
            }

            $update = Posts::where('id', $id)->delete(); 
            $msg['status'] = true;

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }

        return $msg;
    }

    public static function filterPeopleWant($input)
    {

        $user_id = Auth::user()->id;

        $caption = null;
        if (!is_null($input['caption'])) {
            $caption = " and LOWER(p.caption) LIKE '%" . strtolower($input['caption']) . "%' ";
        }

        $username = null;
        if (!is_null($input['username'])) {
            $username = " and LOWER(u_1.name) LIKE '%" . strtolower($input['username']) . "%' ";
        }
        
        $giveStatus = $input['giveStatus'];
        if($giveStatus == 'active'){
            $giveStatus = " and p.give_status = '$giveStatus' ";
        }else{
            $giveStatus = " and p.give_status is null and i.accept_status='$giveStatus' ";
        }
        
        $sortBy = $input['shortBy'];
        $orderBy = $input['orderBy'];
        if($orderBy=='wants'){
            $orderBy = " $orderBy ";
        }else{
            $orderBy = " p.$orderBy ";
        }
        $numPage = $input['numPage'];

        $sql = "
            select count(i.post_id) as wants, p.id,p.give_status,p.give_date, p.created_at,p.updated_at, p.photo, p.caption, p.status, u.id as user_id, u.avatar, u.name
             from ineed as i
            join posts as p on (p.id=i.post_id and p.status='active')
            join users as u_1 on (u_1.id=i.user_id and u_1.status='active')
            join users as u on (u.id=p.user_id and u.status='active')
            where
            i.status=1
            $giveStatus
            $caption 
            $username
            group by i.post_id,p.id,p.give_status,p.give_date, p.created_at,p.updated_at,
             p.photo, p.caption, p.status,user_id, u.avatar, u.name
            order by $orderBy $sortBy
            limit $numPage
        ";
        $data = DB::select($sql);

        return $data;

    }

    public static function peopleWantDetail($input){
        $post_id = $input['id'];
        $sql = "
            select u.name,u.id as user_id,u.avatar,i.created_at,i.accept_date,i.accept_status,i.id as want_id,i.desc from ineed as i
            join posts as p on (p.id=i.post_id and p.status='active')
            join users as u on (u.id=i.user_id and u.status='active')
            where
            i.status=1 and
            i.post_id=$post_id
            order by i.created_at asc
        ";
        $data = DB::select($sql);

        return $data;
    }

    public static function peopleWantSelect($input){
        try {
 
            $id = $input->id;
            $desc = "Congratulation, You're selected.";

            $want = Ineed::where('id',$id)->where('accept_status','pending')
            ->where('request_status','active')->where('status',1)->first();
            if($want){

                $selectedWant = Ineed::where('post_id',$want->post_id)->where('accept_status','selected')
                ->where('request_status','active')->where('status',1)->first();
                if($selectedWant){
                    $_oldIneed['accept_status'] = 'pending';
                    $_oldIneed['accept_date'] = now();
                    $_oldIneed['accept_desc'] = 'Changed!';
                    $selectedWant->update($_oldIneed);
                }

                $find = Posts::whereNull('give_status')
                ->where('status','active')
                ->where('id', $want->post_id)->first();
                if($find){
                    // $data['give_status'] = 'active';
                    // $data['give_date'] = now();
                    // $update = $find->update($data);
    
                    // update ineed
                    $_upIneed['accept_status'] = 'selected';
                    $_upIneed['accept_date'] = now();
                    $_upIneed['accept_desc'] = $desc;
                    $ineed = $want->update($_upIneed);
    
                }else{
                    throw new \Exception('Post is not found!');
                }
            }else{
                throw new \Exception('Want is not found!');
            }
            
            
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
