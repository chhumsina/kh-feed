<?php

namespace App\Http\Controllers\api;

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
        $id = $input->id;

        $data = User::where('id', $id)->first();

        return response()->json($data);
    }

    public function list(Request $input)
    {
        $list_type = $input['list_type'];

        if($list_type == 'recommend'){
            $data = User::take(6)->get();
        }else{
            $data = User::take(1)->get();
        }

        return response()->json($data);
    }

    public function updateOverview(Request $input){
        try{
            DB::beginTransaction();

            $update = User::updateOverView($input);

            if(!$update){
                throw new \Exception('Could not update, Please try again!');
            }

            if($input['name'] != ''){
                $user = User::getUser(Auth::user()->id);
                $colorRandom = rand(1,15);
                $avatar = new Avatar();
                $backgroundRandom = Config::get('laravolt.avatar.backgrounds');
                $avatar->create($input['name'])->setBackground($backgroundRandom[$colorRandom])->save(public_path('/avatar/'.$user->avatar), 100);

            }

            DB::commit();

            $user = User::getUser(Auth::user()->id);
            return response()->json($user);

        }catch (\Exception $e){
            DB::rollBack();
            return response()->json([]);
        }
    }

}
