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

}
