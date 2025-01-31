<?php

namespace App\Http\Controllers\api;

use App\Models\Books;
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

    public function getShop(){
        try{

            $type = 'book';
            $shop = Pages::getShop($type);

            $msg['data'] = $shop;
            $msg['msg'] = 'Get successfully.';
            $msg['status'] = true;
            return response()->json($msg);

        }catch (\Exception $e){
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

    public function createShop(Request $input){
        try{
            DB::beginTransaction();
            $userId = Auth::user()->id;

            $type = 'book';
            $shop = Pages::where('user_id',$userId)->where('page_type', $type)->where('status','active')->first();
            if(is_null($shop)){
                $create = Pages::createShop($input,$type);
            }else{
                throw new \Exception('You already created '.$type.' shop!');
            }

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
