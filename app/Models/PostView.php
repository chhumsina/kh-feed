<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PostView extends Model
{
    protected $table = 'post_view';
    protected $primaryKey = 'id';

    protected $fillable = [
        'post_id','user_id','type'
    ];

    public static function createPostView($post_id){
        $user_id = Auth::user()->id;

        $data['type'] = 'post';
        $data['user_id'] = $user_id;
        $data['post_id'] = $post_id;

        $create = PostView::create($data);
        return $create;

    }

    public static function numView($post_id){
        $sql = "
                select count(id)as num_view from post_view
                where type='post' and post_id=$post_id
                ";
        $data = DB::select($sql);
        return $data;
    }

    public static function lastRead($post_id){
        $sql = "
                select created_at from post_view
                where type='post' and post_id=$post_id
                order by id desc
                limit 1,1
                ";
        $data = DB::select($sql);
        if(isset($data[0]->created_at)){
            $data = Self::timeAgo($data[0]->created_at);
        }else{
            $data = 'just now';
        }
        return $data;
    }

    public static function timeAgo($datetime, $full = false) {
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'mon',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hr',
            'i' => 'min',
            's' => 'sec',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . '' : 'just now';
    }
}
