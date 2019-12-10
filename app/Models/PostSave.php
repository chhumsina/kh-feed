<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostSave extends Model
{
    protected $table = 'post_save';
    protected $primaryKey = 'id';

    protected $fillable = [
        'post_id','user_id','status'
    ];

    public static function createPostSave($post_id){
        try{
            $user_id = Auth::user()->id;

            $find = PostSave::where('user_id', $user_id)->where('post_id', $post_id)->first();
            if($find){
                $data['status'] = true;
                $create = $find->update($data);
            }else{
                $data['status'] = true;
                $data['user_id'] = $user_id;
                $data['post_id'] = $post_id;
                $create = PostSave::create($data);
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
