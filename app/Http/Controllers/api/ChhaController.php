<?php

namespace App\Http\Controllers\api;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Chha;
use Illuminate\Support\Facades\DB;

class ChhaController extends Controller
{
    public function filterPost(Request $input)
    {
        $data = Chha::filterPost($input);

        return response()->json($data);
    }
    
    public function postChangeStatus(Request $input){
        try{

            if(!Chha::isChha()){
                throw new \Exception('Sorry, You are not Chha! :)');
            }

            DB::beginTransaction();

            $status = Chha::postChangeStatus($input);

            if(!$status['status']){
                throw new \Exception('Could not change status, Please try again! '.$status['msg']);
            }

            DB::commit(); 
            $msg['msg'] = 'Changed status successfully.';
            $msg['status'] = true;
            $msg['data'] = null;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

    public function postDelete(Request $input){
        try{

            if(!Chha::isChha()){
                throw new \Exception('Sorry, You are not Chha! :)');
            }

            DB::beginTransaction();

            $status = Chha::postDelete($input);

            if(!$status['status']){
                throw new \Exception('Could not delete, Please try again! '.$status['msg']);
            }

            DB::commit(); 
            $msg['msg'] = 'Deleted successfully.';
            $msg['status'] = true;
            $msg['data'] = null;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

    public function filterPeopleWant(Request $input)
    {
        $data = Chha::filterPeopleWant($input);

        return response()->json($data);
    }

    public function peopleWantDetail(Request $input)
    {
        $data = Chha::peopleWantDetail($input);

        return response()->json($data);
    }

    public function peopleWantSelect(Request $input){
        try{

            if(!Chha::isChha()){
                throw new \Exception('Sorry, You are not Chha! :)');
            }

            DB::beginTransaction();

            $status = Chha::peopleWantSelect($input);

            if(!$status['status']){
                throw new \Exception('Could not select, Please try again! '.$status['msg']);
            }

            DB::commit(); 
            $msg['msg'] = 'Selected successfully.';
            $msg['status'] = true;
            $msg['data'] = null;
            return response()->json($msg);

        }catch (\Exception $e){
            DB::rollBack();
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
            return response()->json($msg);
        }
    }

}
