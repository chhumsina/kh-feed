<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PostFileDownload extends Model
{
    protected $table = 'post_file_download';
    protected $primaryKey = 'id';

    protected $fillable = [
        'post_file_id','user_id'
    ];
    public static function createFileDownload($file_id)
    {
        try{

            $downFile = PostFiles::where('file',$file_id)->first();
            $down_id = $downFile->id;

            $userId = Auth::user()->id;

            $data['post_file_id'] = $down_id;
            $data['user_id'] = $userId;
            $downFile = PostFileDownload::create($data);

            $msg['status'] = true;

        }catch (\Exception $e){
            $msg['msg'] = $e->getLine().':'.$e->getMessage();
            $msg['status'] = false;
        }

        return $msg;

    }
}
