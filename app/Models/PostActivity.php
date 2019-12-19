<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostActivity extends Model
{
    protected $table = 'user_activity';
    protected $primaryKey = 'id';

    protected $fillable = [
        'object_id','user_id','object_type'
    ];

    public static function createPostActivity($obj_id, $obj_type){
        try{
            $user_id = Auth::user()->id;

            $last = PostActivity::where('user_id', $user_id)->orderBy('updated_at','DESC')->first();
            if($last){
                if((($last->object_type == 'post' && $obj_type == 'post') && ($last->object_id==$obj_id))) {
                    $data['updated_at'] = now();
                    $create = $last->update($data);
                }elseif((($last->object_type == 'profile' && $obj_type == 'profile') && ($last->object_id==$obj_id))){
                    $data['updated_at'] = now();
                    $create = $last->update($data);
                }elseif((($last->object_type == 'recommend' && $obj_type == 'recommend') && ($last->object_id==$obj_id))){
                    $data['updated_at'] = now();
                    $create = $last->update($data);
                }else{
                    $data['user_id'] = $user_id;
                    $data['object_id'] = $obj_id;
                    $data['object_type'] = $obj_type;
                    $create = PostActivity::create($data);
                }

            }else{
                $data['user_id'] = $user_id;
                $data['object_id'] = $obj_id;
                $data['object_type'] = $obj_type;
                $create = PostActivity::create($data);
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
