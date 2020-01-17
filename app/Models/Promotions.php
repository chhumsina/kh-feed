<?php

namespace App\Models;

use App\Jobs\SendEmailJob;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage; 

class Promotions extends Model
{ 
    protected $table = 'promotions';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id', 'obj_id', 'status', 'obj_type','type'
    ];


    public static function cam100neak(){
        $data = DB::table('promotions as p')
                ->join('users as u','u.id','=','p.user_id')->where('p.status',1)->where('p.type','first_users')
                ->select('u.*','p.created_at as date')
                ->get();
        return $data;
    }

    public static function becomePeopleGetBook($input){
        
        try{
            $data['user_id'] = $input['user_id'];
            $data['obj_id'] = $input['obj_id'];
            $data['obj_type'] = $input['obj_type'];
            $data['status'] = true;
            $data['type'] = $input['type'];
    
            $create = Promotions::create($data);

            $msg['status'] = true;

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage().$e->getLine();
            $msg['status'] = false;
        }

        return $msg;

    }

}
