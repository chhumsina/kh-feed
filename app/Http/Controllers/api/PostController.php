<?php

namespace App\Http\Controllers\api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PostController extends Controller
{
    public function list()
    {
        $data = DB::table('migrations')->get();
        return response()->json($data);
    }

    public function detail(Request $input)
    {
        $id = $input['id'];
        $data = DB::table('migrations')->where('id',$id)->first();
        return response()->json($data);
    }
}
