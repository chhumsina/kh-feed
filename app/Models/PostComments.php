<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PostComments extends Model
{
    protected $table = 'post_comments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'post_id','comment_id','user_id','status'
    ];

    public static function createPostComment($comm_id, $post_id){
        $user_id = Auth::user()->id;

        $data['status'] = true;
        $data['user_id'] = $user_id;
        $data['comment_id'] = $comm_id;
        $data['post_id'] = $post_id;

        $create = PostComments::create($data);
        return $create;

    }
}
