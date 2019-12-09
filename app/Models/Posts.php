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
        'caption', 'photo', 'status', 'user_id'
    ];

    public static function listPost($input)
    {

        $list_type = $input['list_type'];

        if ($list_type == 'recommend') {
            $data = Posts::take(6)->get();
        } else {
            $search = null;
            if (isset($input['search'])) {
                $search = " and p.caption LIKE '%" . $input['search'] . "%' ";
            }

            $take = 10;
            $page = ($input['page'] - 1) * $take;

            $post_by = null;
            if ($input['id']) {
                $post_by = " and p.user_id = " . $input['id'];
            }

            $sql = "
               select p.id as post_id, p.created_at, p.photo, p.caption, p.status, u.id as user_id, u.avatar, u.name from posts as p
                join users as u on u.id=p.user_id
                where p.status = true
                $search
                $post_by
                group by p.photo, p.id, p.caption, p.status, u.id, u.avatar, u.name, p.created_at
                order by p.id desc
                limit $page,$take
            ";
            $data = DB::select($sql);
        }

        return $data;

    }

    public static function detail($id)
    {

        $sql = "
               select p.created_at, p.photo, p.caption, p.status, p.id, u.id as user_id, u.avatar, u.name from posts as p
                join users as u on u.id=p.user_id 
                where p.id = $id
                group by  p.id, p.photo, p.caption, p.status, u.id, u.avatar, u.name, p.created_at
                order by p.id desc
                limit 1
            ";
        $data = DB::select($sql);

        $view = PostView::createPostView($id);

        return $data;
    }

    public static function createPost($input)
    {
        try {
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
            $data['caption'] = Self::generateCaption($input_data->caption);
            $data['photo'] = $photo_name;
            $data['user_id'] = $userId;
            $data['status'] = true;
            $data = array_filter($data);
            $create_post = Posts::create($data);
            $post_id = $create_post->id;
            $msg['status'] = true;

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }

        return $msg;

    }

    public static function generateCaption($string)
    {
        $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
        $string = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
        return $string;
    }

}
