<?php

namespace App\Http\Controllers\api;

use App\Models\Posts;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Laravolt\Avatar\Avatar;

class PostController extends Controller
{
    public function list(Request $input)
    {
        $list_type = $input['list_type'];

        if($list_type == 'recommend'){
            $data = Posts::take(6)->get();
        }else{
            $sql = "select p.*, u.id, u.avatar, u.name from posts as p
                    join users as u on u.id=p.user_id order by p.id desc";
            $data = DB::select($sql);
        }

        return response()->json($data);
    }

    public function detail(Request $input)
    {
        $id = $input['id'];
        $data = Posts::where('id',$id)->first();
        return response()->json($data);
    }


    public function createPost(Request $input){
        try{
            DB::beginTransaction();

            $create = Posts::createPost($input);

            if(!$create['status']){
                throw new \Exception('Could not create, Please try again! '.$create['msg']);
            }

            DB::commit();

            $msg['msg'] = 'Created successfully.';
            $msg['status'] = true;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

}
