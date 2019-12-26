<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Pages extends Model
{
    protected $table = 'pages';
    protected $primaryKey = 'id';

    protected $fillable = [
        'user_id','name', 'email', 'avatar','phone','level','page_type','status',
        'is_verify','address'
    ];


    public static function createShop($input){
        try {
            $userId = Auth::user()->id;

            $input_data = json_decode($input->data);

            $photo_name = 'no';
            if (isset($input_data->photo)) {
                $base64_image = $input_data->photo;
                if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                    $data_img = substr($base64_image, strpos($base64_image, ',') + 1);

                    $photo_name = uniqid('book_1' . $userId . '_') . ".png";

                    $data_img = base64_decode($data_img);
                    Storage::disk('page')->put($photo_name, $data_img);
                    if (!Storage::disk('page')->exists($photo_name)) {
                        throw new \Exception('Cannot create shop!');
                    }
                }
            }

            $data['avatar'] = $photo_name;
            $data['user_id'] = $userId;
            $data['level'] = 1;
            $data['page_type'] = 'book';
            $data['is_verify'] = false;
            $data['name'] = $input_data->name;
            $data['phone'] = $input_data->phone;
            $data['email'] = $input_data->email;
            $data['address'] = $input_data->address;
            $data['status'] = 'active';
            $create_post = Pages::create($data);
            $msg['status'] = true;

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage().$e->getLine();
            $msg['status'] = false;
        }

        return $msg;

    }

}
