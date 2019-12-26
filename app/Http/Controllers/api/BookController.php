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

class BookController extends Controller
{

    public function sellBook(Request $input){
        try{
            DB::beginTransaction();

            $create = Books::sellBook($input);

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

    public function detail(Request $input)
    {
        $id = $input['id'];
        $data['detail'] = Books::detail($id);
        $data['num_view'] = PostView::numView($id,'book');
        $data['last_read'] = PostView::lastRead($id,'book');
        return response()->json($data);
    }

    public function bookList(Request $input){
        $data = Books::listBook($input);

        return response()->json($data);
    }

}
