<?php

namespace App\Http\Controllers\api;

use App\Models\PostFileDownload;
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

        $data = Posts::listPost($input);

        return response()->json($data);
    }

    public function detail(Request $input)
    {
        $id = $input['id'];
        $data = Posts::detail($id);
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

        }catch (\Exception $e){dd(33);
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

    public function downloadFile(Request $input){
        try{
            DB::beginTransaction();
            $file_id = $input['id'];
            $file = public_path().'/file/'.$file_id;

            $down = PostFileDownload::createFileDownload($file_id);
            if($down['status'] == false){
                throw new \Exception($down['msg']);
            }

            DB::commit();

            return response()->download($file);

        }catch (\Exception $e){dd($e->getMessage());
            DB::rollBack();
            return false;
        }
    }

}
