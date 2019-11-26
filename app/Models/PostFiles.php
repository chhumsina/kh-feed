<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class PostFiles extends Model
{
    protected $fillable = [
        'file','post_id','status'
    ];
    public static function createFiles($post_id, $input)
    {
        try{

            $data = [];
            if ($input->hasFile('file1')) {
                $file = $input->file('file1');
                $file1_name = uniqid('file_1' . $post_id . '_') . "." . $file->getClientOriginalExtension();
                Storage::disk('file')->put($file1_name, File::get($file));
                if (!Storage::disk('file')->exists($file1_name)) {
                    return false;
                }

                array_push($data,['file'=>$file1_name,'post_id'=>$post_id,'status'=>true]);
            }
            $file2_name = null;
            if ($input->hasFile('file2')) {
                $file = $input->file('file2');
                $file2_name = uniqid('file_2' . $post_id . '_') . "." . $file->getClientOriginalExtension();
                Storage::disk('file')->put($file2_name, File::get($file));
                if (!Storage::disk('file')->exists($file2_name)) {
                    return false;
                }
                array_push($data,['file'=>$file2_name,'post_id'=>$post_id,'status'=>true]);
            }
            $file3_name = null;
            if ($input->hasFile('file3')) {
                $file = $input->file('file3');
                $file3_name = uniqid('file_3' . $post_id . '_') . "." . $file->getClientOriginalExtension();
                Storage::disk('file')->put($file3_name, File::get($file));
                if (!Storage::disk('file')->exists($file3_name)) {
                    return false;
                }
                array_push($data,['file'=>$file3_name,'post_id'=>$post_id,'status'=>true]);
            }

            PostFiles::insert($data);

            $msg['status'] = true;

        }catch (\Exception $e){dd($e->getMessage());
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }

        return $msg;

    }
}
