<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\In;
use mysql_xdevapi\Exception;

class Ineed extends Model
{
    protected $table = 'ineed';
    protected $primaryKey = 'id';

    protected $fillable = [
        'post_id','user_id','status','request_status','request_date','accept_status','accept_date','desc','accept_desc'
    ];

    public static function createIneed($input){
        try{
            $user_id = Auth::user()->id;
            $post_id = $input->id;

            $find = Ineed::where('user_id', $user_id)->where('post_id', $post_id)->first();
            if($find){
                $data['desc'] = $input->desc;
                $create = $find->update($data);
            }else{

                if(Helper::hasEmoji($input->desc)){
                    throw new \Exception('សុំទោស មិនទាន់អាចមានអក្សរជា emoji នៅឡើយទេ។');
                }

                if(strlen($input->desc) > 500){
                    throw new \Exception('មតិ គឺសុំមានតួអក្សរតិចជាង៥០០តួ។');
                }

                $data['desc'] = $input->desc;
                $data['status'] = true;
                $data['request_status'] = 'active';
                $data['request_date'] = now();
                $data['accept_status'] = 'pending';
                $data['accept_date'] = now();
                $data['user_id'] = $user_id;
                $data['post_id'] = $post_id;
                $create = Ineed::create($data);
            }
            $msg['status'] = true;
        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }


        return $msg;
    }

    public static function iNeedList($input){
        $post_id = $input->id;
        $sql = "
            select i.*, u.avatar, u.name, u.phone, u.id as user_id from ineed as i
            join users as u on (i.user_id=u.id and u.status='active')
            where i.post_id=$post_id
            order by i.created_at ASC
        ";

        $data = DB::select($sql);
        return $data;
    }

    public static function iNeedRequestList($input)
    {

        $user_id = Auth::user()->id;

        $search = null;
        if (isset($input['search'])) {
            $search = " and LOWER(p.caption) LIKE '%" . strtolower($input['search']) . "%' ";
        }

        $take = 10;
        $page = ($input['page'] - 1) * $take;

        $sql = "
               select i.*, u.avatar, u.name, u.id as user_id, p.id as post_id, p.caption, p.photo, p.created_at as post_date from ineed as i
                join users as u on (i.user_id=u.id and u.status='active')
                join posts as p on (i.post_id=p.id and p.status='active')
                where 
                i.user_id = $user_id
                $search
                order by i.created_at desc
                limit $page,$take
            ";
        $data = DB::select($sql);

        return $data;

    }

    public static function createSubmitGiveTo($input){
        try{
            $user_id = Auth::user()->id;
            $post_id = $input->id;
            $desc = $input->desc;
            $giveto = $input->giveto;

            $find = Posts::where('user_id', $user_id)
                    ->whereNull('give_status')
                    ->where('status','active')
                    ->where('id', $post_id)->first();
            if($find){
                $data['give_status'] = 'active';
                $data['give_date'] = now();
                $update = $find->update($data);

                // update ineed
                $_upIneed['accept_status'] = 'active';
                $_upIneed['accept_date'] = now();
                $_upIneed['accept_desc'] = $desc;
                $ineed = Ineed::where('post_id', $post_id)->where('accept_status','pending')
                    ->where('request_status','active')->where('status',1)->whereIn('user_id',$giveto)->update($_upIneed);

            }else{
                throw new \Exception('Cannot find this contributed book!');
            }
            $msg['status'] = true;
        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }

        return $msg;
    }

}
