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
use App\Models\Promotions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Laravolt\Avatar\Avatar;

class PromotionController extends Controller
{
    public function campaign(Request $input){
        $type = $input['type'];
        $callFunc = Promotions::$type();
        return response()->json($callFunc);
    }

    public function becomePeopleGetBook(Request $input){
        try{
            DB::beginTransaction();

            $user_id = Auth::user()->id;
            $type = 'first_users';
            $phone = $input['phone'];

            $user = User::where('id',$user_id)->where('status','active')->first();

            if($user){

                $user->update(['phone'=>$phone]);

                $checkAvailable = Promotions::where('status',true)->where('type',$type)->count();
                if($checkAvailable < 100){
                    $findPro = Promotions::where('user_id',$user_id)->where('status',true)->where('type',$type)->first();
                    if($findPro){
                        $msg['msg'] = 'អ្នកបានដាក់បញ្ចូលក្នុងចំណោមមនុស្ស១០០ដំបូងរួចហើយ។';
                    }else{
                        $data['user_id'] = $user_id;
                        $data['obj_id'] = $user_id;
                        $data['obj_type'] = 'user';
                        $data['type'] = $type;
                        $create = Promotions::becomePeopleGetBook($data);
                        $msg['msg'] = 'ជោគជ័យ!​​ អ្នកបានដាក់បញ្ចូលក្នុងចំណោមមនុស្ស១០០ដំបូងហើយ​។';
        
                        if(!$create['status']){
                            throw new \Exception('សុំទោស មិនអាចទទួលបាន។​ សូមជួយ Feedback។');
                        }
                    }
                }else{
                    throw new \Exception('សុំទោស អ្នកមកក្រោយគេបន្តិចហើយ។');
                }
                
            }else{
                throw new \Exception('សុំទោស មិនអាចទទួលបាន។​ សូមជួយ Feedback។');
            }

            DB::commit();

        
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
