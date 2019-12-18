<?php

namespace App\Http\Controllers\api;

use App\Models\PostActivity;
use App\Models\PostView;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Laravolt\Avatar\Avatar;

class UserController extends Controller
{
    public function account(Request $input)
    {
        $account_id = $input->id;

        $data = User::where('id', $account_id)->first();
        try {
            DB::beginTransaction();

            $view = PostView::createPostView($account_id, 'profile');
            $activity = PostActivity::createPostActivity($account_id, 'profile');
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
        }

        return response()->json($data);
    }

    public function list(Request $input)
    {
        $list_type = $input['list_type'];

        if($list_type == 'recommend'){
            $data = User::take(6)->get();
        }else{
            $data = User::get();
        }

        return response()->json($data);
    }

    public function updateOverview(Request $input){
        try{
            DB::beginTransaction();

//            $userId = Auth::user()->id;
//            $user = User::where('id', $userId)->first()->name;
//            if($input['name'] != '' && $input['name'] != $user){
//                $user = User::getUser(Auth::user()->id);
//                $colorRandom = rand(1,14);
//                $avatar = new Avatar();
//                $backgroundRandom = Config::get('laravolt.avatar.backgrounds');
//                $avatar->create($input['name'])->setBackground($backgroundRandom[$colorRandom])->save(public_path('/avatar/'.$user->avatar), 100);
//
//            }

            $update = User::updateOverView($input);

            if(!$update){
                throw new \Exception('Could not update, Please try again!');
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

    public function changeAvatar(Request $input){
        try{
            DB::beginTransaction();

            $update = User::changeAvatar($input);

            if(!$update){
                throw new \Exception('Could not change, Please try again!');
            }

            DB::commit();

            $msg['msg'] = 'Changed successfully.';
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
