<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Posts extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'caption','title','photo','status','user_id'
    ];

    public static function listPost($input){

        $list_type = $input['list_type'];

        if($list_type == 'recommend'){
            $data = Posts::take(6)->get();
        }else{
            $search = null;
            if(isset($input['search'])){
                $search = " WHERE p.title LIKE '%".$input['search']."%' ";
            }

            $take = 10;
            $page = ($input['page']-1)*$take;

            $sql = "
               select GROUP_CONCAT(pf.file ORDER BY pf.id ASC SEPARATOR ', ') as files, count(pf.id) as num_download_file, p.id as post_id, p.created_at, p.title, p.photo, p.caption, p.status, u.id, u.avatar, u.name from posts as p
                join users as u on u.id=p.user_id 
                left join post_files as pf on pf.post_id=p.id
                $search
                group by  p.title, p.photo, p.id, p.caption, p.status, u.id, u.avatar, u.name, p.created_at
                order by p.id desc
                limit $page,$take
            ";
            $data = DB::select($sql);
        }

        return $data;

    }

    public static function detail($id){

        $sql = "
               select GROUP_CONCAT(pf.file ORDER BY pf.id ASC SEPARATOR ', ') as files, count(pf.id) as num_download_file, p.created_at, p.title, p.photo, p.caption, p.status, p.id, u.id as user_id, u.avatar, u.name from posts as p
                join users as u on u.id=p.user_id 
                left join post_files as pf on pf.post_id=p.id
                where p.id = $id
                group by  p.title, p.id, p.photo, p.caption, p.status, u.id, u.avatar, u.name, p.created_at
                order by p.id desc
                limit 1
            ";
        $data = DB::select($sql);

        return $data;
    }

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
            $create_post = Posts::create($data);
            $post_id = $create_post->id;
            $post_file = PostFiles::createFiles($post_id, $input);
            if(!$post_file['status']){
                throw new \Exception('Could not create, Please try again! '.$post_file['msg']);
            }

            $msg['status'] = true;

        }catch (\Exception $e){dd(222);
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }

        return $msg;

    }
}
