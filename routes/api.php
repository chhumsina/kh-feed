<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => '/auth', ['middleware' => 'throttle:20,5']], function() {
    Route::post('/register', 'Auth\RegisterController@register');
    Route::post('/login', 'Auth\LoginController@login');

    Route::get('/login/{service}', 'Auth\SocialLoginController@redirect');
    Route::get('/login/{service}/callback', 'Auth\SocialLoginController@callback');
});

Route::get('image/{type}/{size}/{img}', function($type,$size,$img)
{
    $img  = Image::make(public_path('/'.$type.'/'.$img));
    if($size == 'sm_post'){
        $resize = $img->resize(100, 70);
    }elseif($size == 'm_post'){
        $resize = $img->resize(350, 200);
    }elseif($size == 'sm_avatar'){
        $resize = $img->resize(80, 80);
    }elseif($size == 'm_avatar'){
        $resize = $img->resize(150, 150);
    }

    return $resize->response('png');
});


Route::group(['middleware' => 'jwt.auth'], function() {
    Route::get('account/{id}', 'UserController@account');
    Route::get('user/list', 'UserController@list');
    Route::get('/me', 'MeController@index');
    Route::get('post/list', 'PostController@list');
    Route::get('post/save-list', 'PostController@saveList');
    Route::get('post/detail/{id}', 'PostController@detail');

    Route::post('post/delete-post', 'PostController@deletePost');
    Route::post('post/save-post', 'PostController@savePost');
    Route::post('post/unsave-post', 'PostController@unSavePost');
    Route::get('post/detail/{id}', 'PostController@detail');
    Route::get('post/detail-comment/{id}', 'PostController@detailComment');

    Route::post('update-overview', 'UserController@updateOverview');
    Route::post('create-post', 'PostController@createPost');
    Route::post('create-comment', 'PostController@createComment');

    Route::get('file/{id}', 'PostController@downloadFile');

    Route::get('/auth/logout', 'MeController@logout');

});
