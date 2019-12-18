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
                $search = " and LOWER(p.caption) LIKE '%" . strtolower($input['search']) . "%' ";
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
                where p.status = 'active'
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

    public static function listSavePost($input)
    {

        $user_id = Auth::user()->id;

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
                select p.id as post_id,ps.updated_at as created_at, p.photo, p.caption, u.id as user_id, u.avatar, u.name from post_save as ps
                join users as u on u.id=ps.user_id
                join posts as p on p.id=ps.post_id
                where p.status = 'active'
                and ps.user_id=$user_id
                $search
                $post_by
                group by p.photo, p.id, p.caption, p.status, u.id, u.avatar, u.name, ps.updated_at
                order by ps.updated_at desc
                limit $page,$take
            ";
        $data = DB::select($sql);

        return $data;

    }

    public static function listActivity($input)
    {

        $user_id = Auth::user()->id;

        $take = 10;
        $page = ($input['page'] - 1) * $take;

        $sql = "
                select * from 
                    (
                    select ua.object_type as type, ua.updated_at as activity_date, p.id as post_id, p.caption,
                    p.photo, CONCAT('Commented: ',c.description) as description
                    from user_activity as ua
                    join post_comments as pc on (pc.comment_id=ua.object_id and pc.status=1 and pc.user_id=$user_id)
                    join comments as c on c.id=pc.comment_id
                    join posts as p on (p.id=pc.post_id and p.status='active')
                    where
                    ua.user_id=$user_id
                    and ua.object_type='comment'
                    
                    UNION
                    
                    select ua.object_type as type, ua.updated_at as activity_date, p.id as post_id, p.caption,
                    p.photo, 'Viewed post' as description
                    from user_activity as ua
                    join posts as p on (p.id=ua.object_id and p.status='active')
                    where ua.user_id=$user_id
                    and ua.object_type='post'
                    ) as activity order by activity_date desc
                limit $page,$take
            ";
        $data = DB::select($sql);

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

        try {
            DB::beginTransaction();

            $view = PostView::createPostView($id);
            $activity = PostActivity::createPostActivity($id, 'post');

            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
        }

        return $data;
    }

    public static function createPost($input)
    {
        try {
            $userId = Auth::user()->id;

            $input_data = json_decode($input->data);

            $photo_name = 'no';
            if (isset($input_data->photo)) {
                $base64_image = $input_data->photo;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                    $data_img = substr($base64_image, strpos($base64_image, ',') + 1);

                    $photo_name = uniqid('photo_1' . $userId . '_') . ".png";

                    $data_img = base64_decode($data_img);
                    Storage::disk('photo')->put($photo_name, $data_img);
                    if (!Storage::disk('photo')->exists($photo_name)) {
                        throw new \Exception('Cannot create post!');
                    }
                }
            }

            $data['caption'] = Self::generateCaption($input_data->caption);
            $data['photo'] = $photo_name;
            $data['user_id'] = $userId;
            $data['status'] = 'active';
            $data = array_filter($data);
            $create_post = Posts::create($data);
            $post_id = $create_post->id;
            $msg['status'] = true;

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage().$e->getLine();
            $msg['status'] = false;
        }

        return $msg;

    }

    public static function deletePost($postId){
        try {
            $userId = Auth::user()->id;

            $post = Posts::where('user_id', $userId)->where('id', $postId)->first();
            if($post){
                $data['status'] = 'delete';
                $post = $post->update($data);
            }
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
