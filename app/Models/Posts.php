<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class Posts extends Model
{
    protected $table = 'posts';
    protected $primaryKey = 'id';

    protected $fillable = [
        'caption', 'photo', 'status', 'user_id','give_status','give_date'
    ];

    public static function listPost($input)
    {
        $user_id = Auth::user()->id;

        $search = null;
            if (isset($input['search'])) {
                $search = " and LOWER(p.caption) LIKE '%" . strtolower($input['search']) . "%' ";
            }

            $take = 10;
            $page = ($input['page'] - 1) * $take;

            $status = "p.status = 'active'";
            $post_by = null;
            if ($input['id']) {
                $post_by = " and p.user_id = " . $input['id'];

                if($user_id == $input['id']){
                    $status = "p.status in ('active','pending')";
                }
            }


            $sql = "
               select p.give_status, p.give_date, p.id as post_id, p.created_at, p.photo, p.caption, p.status, u.id as user_id, u.avatar, u.name from posts as p
                join users as u on u.id=p.user_id
                where 
                $status
                $search
                $post_by
                order by p.id desc
                limit $page,$take
            ";
            $data = DB::select($sql);

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

        $take = 15;
        $page = ($input['page'] - 1) * $take;

        $sql_comment = "
                select ua.object_type as type, ua.updated_at as activity_date, p.id as post_id, p.caption,
                    p.photo, CONCAT('Commented: ',c.description) as description
                    from user_activity as ua
                    join post_comments as pc on (pc.comment_id=ua.object_id and pc.status=1 and pc.user_id=$user_id)
                    join comments as c on c.id=pc.comment_id
                    join posts as p on (p.id=pc.post_id and p.status='active')
                    where
                    ua.user_id=$user_id
                    and ua.object_type='comment'
            ";

        $sql_post = "
                select ua.object_type as type, ua.updated_at as activity_date, p.id as post_id, p.caption,
                    p.photo, 'Viewed post' as description
                    from user_activity as ua
                    join posts as p on (p.id=ua.object_id and p.status='active')
                    where ua.user_id=$user_id
                    and ua.object_type='post'
             ";

        $sql_recommend = "
                select ua.object_type as type, ua.updated_at as activity_date, p.id as post_id, p.caption,
                    p.photo, 'Recommended post' as description
                    from user_activity as ua
                    join posts as p on (p.id=ua.object_id and p.status='active')
                    join users as u on (u.id=ua.user_id and u.status='active')
                    where
                    ua.user_id=$user_id
                    and
                    ua.object_type='recommend'
             ";

        $sql_profile = "
                select ua.object_type as type, ua.updated_at as activity_date, u.id as post_id, u.name as caption,
                    u.avatar as photo, 'Viewed profile' as description
                from user_activity as ua
                join users as u on (u.id=ua.object_id and u.status='active' and u.id!=$user_id)
                where
                ua.user_id=$user_id
                and
                ua.object_type='profile'
               ";

        $sql = null;
        if($input['filter_type'] == 'profile'){
            $sql = $sql_profile;
        }elseif($input['filter_type'] == 'recommend'){
            $sql = $sql_recommend;
        }elseif($input['filter_type'] == 'post'){
            $sql = $sql_post;
        }elseif($input['filter_type'] == 'comment'){
            $sql = $sql_comment;
        }else{
            $sql = $sql_profile." UNION ".$sql_post." UNION ".$sql_comment;
        }

        $sqlFinal = "
            select * from 
                (
                    $sql
                ) as activity order by activity_date DESC
                limit $page,$take
        ";

        $data = DB::select($sqlFinal);

        return $data;

    }

    public static function listTopRecommend($input){
        $sql = "
            select count(ua.object_id) as num_recom,p.caption,p.photo, ua.object_id as post_id,u.avatar,u.name from user_activity as ua
            join posts as p on (p.id=ua.object_id and p.status='active')
            join users as u on (u.id=p.user_id and u.status='active')
            where ua.object_type = 'recommend'
            group by p.caption, p.photo, ua.object_id, u.avatar, u.name
            HAVING count(ua.object_id) > 1
            order by num_recom desc
            limit 10
        ";

        $data = DB::select($sql);
        return $data;
    }

    public static function listReaction($input)
    {

        $user_id = Auth::user()->id;

        $take = 15;
        $page = ($input['page'] - 1) * $take;

        $sql_comment = "
                select ua.object_type as type, ua.updated_at as activity_date, p.id as post_id, p.caption,
                CONCAT('Commented: ',c.description) as description,u.id as by_user_id,
                u.avatar as by_user_avatar,u.name as by_user_name
                from user_activity as ua
                join post_comments as pc on (pc.comment_id=ua.object_id and pc.status=1 and pc.user_id!=$user_id)
                join comments as c on c.id=pc.comment_id
                join posts as p on (p.id=pc.post_id and p.status='active' and p.user_id=$user_id)
                join users as u on (u.id=ua.user_id and u.status='active')
                where
                ua.user_id!=$user_id
                and
                ua.object_type='comment'
            ";

        $sql_post = "
                select ua.object_type as type, ua.updated_at as activity_date, p.id as post_id, p.caption,
                'Viewed post' as description,u.id as by_user_id,
                u.avatar as by_user_avatar,u.name as by_user_name
                from user_activity as ua
                join posts as p on (p.id=ua.object_id and p.status='active' and p.user_id=$user_id)
                join users as u on (u.id=ua.user_id and u.status='active')
                where
                ua.user_id!=$user_id
                and
                ua.object_type='post'
             ";

        $sql_recommend = "
                select ua.object_type as type, ua.updated_at as activity_date, p.id as post_id, p.caption,
                'Recommended post' as description,u.id as by_user_id,
                u.avatar as by_user_avatar,u.name as by_user_name
                from user_activity as ua
                join posts as p on (p.id=ua.object_id and p.status='active' and p.user_id=$user_id)
                join users as u on (u.id=ua.user_id and u.status='active')
                where
                ua.user_id!=$user_id
                and
                ua.object_type='recommend'
             ";

        $sql_profile = "
                select ua.object_type as type, ua.updated_at as activity_date, '' as post_id, '' as caption,
                'Viewed profile' as description,u.id as by_user_id,
                u.avatar as by_user_avatar,u.name as by_user_name
                from user_activity as ua
                join users as u1 on (u1.id=ua.object_id and u1.status='active' and u1.id=$user_id)
                join users as u on (u.id=ua.user_id and u.status='active')
                where
                ua.user_id!=$user_id
                and
                ua.object_type='profile'
               ";

        $sql = null;
        if($input['filter_type'] == 'profile'){
            $sql = $sql_profile;
        }elseif($input['filter_type'] == 'recommend'){
             $sql = $sql_recommend;
        }elseif($input['filter_type'] == 'post'){
            $sql = $sql_post;
        }elseif($input['filter_type'] == 'comment'){
            $sql = $sql_comment;
        }else{
            $sql = $sql_profile." UNION ".$sql_post." UNION ".$sql_comment;
        }

        $sqlFinal = "
            select * from 
                (
                    $sql
                ) as activity order by activity_date DESC
                limit $page,$take
        ";

        $data = DB::select($sqlFinal);

        return $data;

    }

    public static function detail($id)
    {

        $sql = "
               select p.created_at, p.photo, p.caption, p.give_status, p.status, p.id, u.id as user_id, u.avatar, u.name from posts as p
                join users as u on u.id=p.user_id 
                where p.id = $id
                order by p.id desc
                limit 1
            ";
        $data = DB::select($sql);

        try {
            DB::beginTransaction();

            $view = PostView::createPostView($id, 'post');
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
            $data['status'] = 'pending';
            $data = array_filter($data);
            $create_post = Posts::create($data);
            $post_id = $create_post->id;
            $msg['status'] = true;


            // send mail
            $details['username'] = Auth::user()->name;
            $details['body'] = $input_data->caption;
            dispatch(new SendEmailJob($details));

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

    public static function recieveBook($id, $user_id){
        try {
 
            $desc = "Congratulation, you had received the book.";

            $want = Ineed::where('id',$id)->where('user_id',$user_id)->where('accept_status','selected')
            ->where('request_status','active')->where('status',1)->first();
            if($want){

                $find = Posts::whereNull('give_status')
                ->where('status','active')
                ->where('id', $want->post_id)->first();
                if($find){
                    $data['give_status'] = 'active';
                    $data['give_date'] = now();
                    $update = $find->update($data);
    
                    // update ineed
                    $_upIneed['accept_status'] = 'active';
                    $_upIneed['accept_date'] = now();
                    $_upIneed['accept_desc'] = $desc;
                    $ineed = $want->update($_upIneed);
    
                }else{
                    throw new \Exception('សុំទោស មិនអាចទទួលសៀវភៅបាន។​ សូមជួយ Feedback!');
                }
            }else{
                throw new \Exception('សុំទោស មិនអាចទទួលសៀវភៅបាន។​ សូមជួយ Feedback!!');
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
