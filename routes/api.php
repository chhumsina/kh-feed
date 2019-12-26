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

    if(in_array($size,['m_avatar','m_post'])){
        return $img->response('png');
    }else{
        if($size == 'sm_post'){
            $resize = $img->resize(40, 40);
        }elseif($size == 'sm_avatar'){
            $resize = $img->resize(40, 40);
        }

        return $resize->response('png');
    }

});


Route::group(['middleware' => 'jwt.auth'], function() {
    Route::get('account/{id}', 'UserController@account');
    Route::get('user/list-user-by-top-view', 'UserController@listUserByTopView');
    Route::get('/me', 'MeController@index');
    Route::get('post/list', 'PostController@list');
    Route::get('post/save-list', 'PostController@saveList');
    Route::get('post/activity-list', 'PostController@activityList');
    Route::get('post/top-recommend-list', 'PostController@topRecommendList');
    Route::get('post/reaction-list', 'PostController@reactionList');
    Route::get('post/detail/{id}', 'PostController@detail');

    Route::post('post/delete-post', 'PostController@deletePost');
    Route::post('post/recommend-post', 'PostController@recommendPost');
    Route::post('post/save-post', 'PostController@savePost');
    Route::post('post/unsave-post', 'PostController@unSavePost');
    Route::get('post/detail/{id}', 'PostController@detail');
    Route::get('post/detail-comment/{id}', 'PostController@detailComment');

    Route::post('user/change-avatar', 'UserController@changeAvatar');
    Route::post('user/update-overview', 'UserController@updateOverview');
    Route::post('create-post', 'PostController@createPost');
    Route::post('create-shop', 'ShopController@createShop');
    Route::post('create-comment', 'PostController@createComment');

    Route::get('file/{id}', 'PostController@downloadFile');

    Route::get('/auth/logout', 'MeController@logout');

});
