<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Comments extends Model
{
    protected $table = 'comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'description'
    ];

    public static function createComment($input)
    {
        try {

            $input_data = json_decode($input->data);
            $data['description'] = $input_data->comment;
            $data = array_filter($data);
            $create_comment = Comments::create($data);
            $comm_id = $create_comment->id;
            $post_id = $input_data->id;
            $postComm = PostComments::createPostComment($comm_id, $post_id);
            $msg['status'] = true;

            $activity = PostActivity::createPostActivity($comm_id, 'comment');

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage();
            $msg['status'] = false;
        }

        return $msg;

    }

    public static function detail($id)
    {

        $sql = "
               select co.created_at, co.description as comments, u.id as user_id, u.avatar, u.name from post_comments as pc
                join comments as co on pc.comment_id=co.id
                join users as u on pc.user_id=u.id
                where pc.status=1 and pc.post_id=$id
                order by co.created_at DESC
            ";
        $data = DB::select($sql);

        return $data;
    }

    public static function numComment($id){
        $sql = "
               select count(id)as num_comment from post_comments
               where post_id=$id
            ";
        $data = DB::select($sql);

        return $data;
    }
    public static function getLastComment(){
        $sql = "
               select co.created_at, co.description as comments, u.id as user_id, u.avatar, u.name from post_comments as pc
                join comments as co on pc.comment_id=co.id
                join users as u on pc.user_id=u.id
                where pc.status=1
                order by co.created_at DESC
                limit 1
            ";
        $data = DB::select($sql);

        return $data;
    }
}
