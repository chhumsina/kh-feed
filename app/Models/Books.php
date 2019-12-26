<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class Books extends Model
{
    protected $table = 'books';
    protected $primaryKey = 'id';

    protected $fillable = [
        'name',
        'user_id',
        'page_id',
        'author',
        'price',
        'currency',
        'photo',
        'num_page',
        'isbn',
        'status',
        'is_out_of_stock',
        'description',
        'outline',
    ];

    public static function sellBook($input)
    {
        try {
            $userId = Auth::user()->id;
            $pageId = Pages::where('user_id', $userId)->where('status','active')->first()->id;

            $input_data = json_decode($input->data);

            $base64_image = $input_data->photo;
            $photo_name = null;
            if (preg_match('/^data:image\/(\w+);base64,/', $base64_image)) {
                $data_img = substr($base64_image, strpos($base64_image, ',') + 1);

                $photo_name = uniqid('book_1' . $userId . '_') . ".png";

                $data_img = base64_decode($data_img);
                Storage::disk('book')->put($photo_name, $data_img);
                if (!Storage::disk('book')->exists($photo_name)) {
                    throw new \Exception('Cannot sell book!');
                }
            }

            $data['description'] = Self::generateCaption($input_data->description);
            $data['photo'] = $photo_name;
            $data['outline'] = $input_data->outline;
            $data['name'] = $input_data->name;
            $data['price'] = $input_data->price;
            $data['currency'] = $input_data->currency;
            $data['author'] = $input_data->author;
            $data['isbn'] = $input_data->isbn;
            $data['num_page'] = $input_data->num_page;
            $data['is_out_of_stock'] = false;
            $data['user_id'] = $userId;
            $data['page_id'] = $pageId;
            $data['status'] = 'active';

            $create_book = Books::create($data);

            $msg['status'] = true;

        } catch (\Exception $e) {
            $msg['msg'] = $e->getMessage().$e->getLine();
            $msg['status'] = false;
        }

        return $msg;

    }

    public static function listBook($input)
    {

        $search = null;
        if (isset($input['search'])) {
            $search = " and LOWER(b.name) LIKE '%" . strtolower($input['search']) . "%' ";
        }

        $take = 9;
        $page = ($input['page'] - 1) * $take;

        $post_by = null;
        if ($input['id']) {
            $post_by = " and b.page_id = " . $input['id'];
        }

        $sql = "
               select b.id,b.name,b.price,b.currency,b.created_at,b.photo,b.is_out_of_stock, p.avatar from books as b
                join pages as p on (p.id=b.page_id and p.status='active')
                where
                b.status='active'
                and p.page_type='book'
                $search
                $post_by
                order by b.created_at DESC
                limit $page,$take
            ";
        $data = DB::select($sql);

        return $data;

    }

    public static function detail($id)
    {

        $sql = "
               select b.*,p.id as page_id, p.avatar,p.name as page_name from books as b
                join pages as p on (p.id=b.page_id and p.status='active')
                where
                b.status='active'
                and p.page_type='book'
                and
                b.id=$id
            ";
        $data = DB::select($sql);

        try {
            DB::beginTransaction();

            $view = PostView::createPostView($id, 'book');

            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
        }

        return $data;
    }

    public static function generateCaption($string)
    {
        $url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
        $string = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a>', $string);
        return $string;
    }

}
