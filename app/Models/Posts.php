<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Posts extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'caption','title','photo','status','user_id'
    ];

    public static function createPost($input)
    {
        try{
            $userId = Auth::user()->id;

            $photo_name = null;
            if ($input->hasFile('photo')) {
                $file = $input->file('photo');
                $photo_name = uniqid('photo_1' . $userId . '_') . "." . $file->getClientOriginalExtension();
                Storage::disk('photo')->put($photo_name, File::get($file));
                if (!Storage::disk('photo')->exists($photo_name)) {
                    return false;
                }
            }

            $input_data = json_decode($input->data);
//            $post = new Posts();
//            $post->caption = $input_data->caption;
//            $post->title = $input_data->title;
//            $post->photo = $photo_name;
//            $post->user_id = $userId;
//            $post->status = true;
//            $post = $post->save();
//            $post_id = $post->id;
            $data['caption'] = $input_data->caption;
            $data['title'] = $input_data->title;
            $data['photo'] = $photo_name;
            $data['user_id'] = $userId;
            $data['status'] = true;
            $data = array_filter($data);
            Posts::insert($data);
            $post_id = Posts::latest()->first()->id;
//dd($post_id);
            $post_file = PostFiles::createFiles($post_id, $input);
            if(!$post_file['status']){
                throw new \Exception('Could not create, Please try again! '.$post_file['msg']);
            }

            $msg['status'] = true;

        }catch (\Exception $e){
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }

        return $msg;

    }
}
