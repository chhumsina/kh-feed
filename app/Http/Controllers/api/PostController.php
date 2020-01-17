<?php

namespace App\Http\Controllers\api;

use App\Models\Comments;
use App\Models\Dashboard;
use App\Models\Ineed;
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

class PostController extends Controller
{
    public function list(Request $input)
    {

        $data = Posts::listPost($input);

        return response()->json($data);
    }

    public function saveList(Request $input)
    {

        $data = Posts::listSavePost($input);

        return response()->json($data);
    }

    public function activityList(Request $input)
    {

        $data = Posts::listActivity($input);

        return response()->json($data);
    }

    public function reactionList(Request $input)
    {

        $data = Posts::listReaction($input);

        return response()->json($data);
    }

    public function topRecommendList(Request $input)
    {

        $data = Posts::listTopRecommend($input);

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
                throw new \Exception($create['msg']);
            }

            DB::commit();
 
            $msg['msg'] = 'សៀវភៅបរិច្ចាគត្រូវបានបញ្ចូន ពួកយើងនឹងធ្វើការទំនាក់ទំនងទៅកាន់អ្នកក្នុងពេលឆាប់ៗនេះ​។​';
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

    public function recieveBook(Request $input){
        try{
            DB::beginTransaction();

            $user_id = auth::user()->id;
            $id = $input['id'];
            $create = Posts::recieveBook($id, $user_id);

            if(!$create['status']){
                throw new \Exception('សុំទោស មិនអាចទទួលសៀវភៅបាន។​ សូមជួយ Feedback! '.$create['msg']);
            }

            DB::commit();

            $msg['msg'] = 'បានទទួលសៀវភៅ ដោយជោគជ័យ។';
            $msg['status'] = true;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

    public function iNeed(Request $input){
        try{
            DB::beginTransaction();

            $create = Ineed::createIneed($input);

            if(!$create['status']){
                throw new \Exception('Could not make I want, Please try again! '.$create['msg']);
            }

            DB::commit();

            $msg['msg'] = 'Submit successfully.';
            $msg['status'] = true;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

    public function submitGiveTo(Request $input){
        try{
            DB::beginTransaction();

            $create = Ineed::createSubmitGiveTo($input);

            if(!$create['status']){
                throw new \Exception('Could not give to, Please try again! '.$create['msg']);
            }

            DB::commit();

            $msg['msg'] = 'Submit successfully, This contributed book has been closed.';
            $msg['status'] = true;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

    public function iNeedList(Request $input)
    {

        $data = Ineed::iNeedList($input);

        return response()->json($data);
    }

    public function iNeedRequestList(Request $input)
    {

        $data = Ineed::iNeedRequestList($input);

        return response()->json($data);
    }

    public function dashboardList(Request $input){
        $type = $input->type;
        $page = $input->page;
        $data = Dashboard::list($type,$page);

        return response()->json($data);
    }

    public function dashboardTotal(Request $input){

        $data = Dashboard::total();

        return response()->json($data);
    }

}
