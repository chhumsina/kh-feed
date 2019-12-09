<?php

namespace App\Http\Controllers\api;

use App\Models\Comments;
use App\Models\PostFileDownload;
use App\Models\Posts;
use App\Models\PostView;
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
        $data['detail'] = Posts::detail($id);
        $data['num_view'] = PostView::numView($id);
        $data['num_comment'] = Comments::numComment($id);
        $data['last_read'] = PostView::lastRead($id);
        return response()->json($data);
    }

    public function detailComment(Request $input)
    {
        $id = $input['id'];
        $data = Comments::detail($id);
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

    public function createComment(Request $input){
        try{
            DB::beginTransaction();

            $create = Comments::createComment($input);

            if(!$create['status']){
                throw new \Exception('Could not create, Please try again! '.$create['msg']);
            }

            DB::commit();

            $data = Comments::getLastComment();
            $msg['msg'] = 'Created successfully.';
            $msg['status'] = true;
            $msg['data'] = $data;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

}
