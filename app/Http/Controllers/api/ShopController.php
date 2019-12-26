<?php

namespace App\Http\Controllers\api;

use App\Models\Comments;
use App\Models\Pages;
use App\Models\PostActivity;
use App\Models\PostFileDownload;
use App\Models\Posts;
use App\Models\PostSave;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Laravolt\Avatar\Avatar;

class ShopController extends Controller
{

    public function createShop(Request $input){
        try{
            DB::beginTransaction();

            $create = Pages::createShop($input);

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

    public function recommendPost(Request $input){
        try{
            DB::beginTransaction();

            $post_id = $input['id'];
            $create = PostActivity::createPostActivity($post_id,'recommend');

            if(!$create['status']){
                throw new \Exception('Could not recommend, Please try again! '.$create['msg']);
            }

            DB::commit();

            $msg['msg'] = 'Recommend successfully.';
            $msg['status'] = true;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }
    public function savePost(Request $input){
        try{
            DB::beginTransaction();

            $post_id = $input['id'];
            $create = PostSave::createPostSave($post_id);

            if(!$create['status']){
                throw new \Exception('Could not save, Please try again! '.$create['msg']);
            }

            DB::commit();

            $msg['msg'] = 'Saved successfully.';
            $msg['status'] = true;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }
    public function unSavePost(Request $input){
        try{
            DB::beginTransaction();

            $post_id = $input['id'];
            $create = PostSave::unSavePost($post_id);

            if(!$create['status']){
                throw new \Exception('Could not unsave, Please try again! '.$create['msg']);
            }

            DB::commit();

            $msg['msg'] = 'Unsaved successfully.';
            $msg['status'] = true;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

    public function deletePost(Request $input){
        try{
            DB::beginTransaction();

            $post_id = $input['id'];
            $create = Posts::deletePost($post_id);

            if(!$create['status']){
                throw new \Exception('Could not delete, Please try again! '.$create['msg']);
            }

            DB::commit();

            $msg['msg'] = 'Deleted successfully.';
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
