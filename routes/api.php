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

    if(in_array($size,['m_avatar','m_post','m_page','m_book'])){
        return $img->response('png');
    }else{
        if($size == 'sm_post'){
            $resize = $img->resize(40, 40);
        }elseif($size == 'sm_avatar'){
            $resize = $img->resize(40, 40);
        }elseif($size == 'sm_page'){
            $resize = $img->resize(40, 40);
        }elseif($size == 'sm_book'){
            $resize = $img->resize(100, 150);
        }

        return $resize->response('png');
    }

});


Route::group(['middleware' => 'jwt.auth'], function() {
    Route::get('account/{id}', 'UserController@account');
    Route::get('user/list-user-by-top-contribution', 'UserController@listUserByTopContribution');
    Route::get('user/list-user-by-top-view', 'UserController@listUserByTopView');
    Route::get('/me', 'MeController@index');
    Route::get('post/list', 'PostController@list');
    Route::get('post/save-list', 'PostController@saveList');
    Route::get('post/activity-list', 'PostController@activityList');
    Route::get('post/top-recommend-list', 'PostController@topRecommendList');
    Route::get('post/reaction-list', 'PostController@reactionList');
    Route::get('post/detail/{id}', 'PostController@detail');


    Route::get('post/dashboard', 'PostController@dashboardList');
    Route::get('post/dashboard-total', 'PostController@dashboardTotal');

    Route::post('post/i-need', 'PostController@iNeed');
    Route::get('post/i-need-list/{id}', 'PostController@iNeedList');
    Route::get('post/i-need-request-list', 'PostController@iNeedRequestList');
    Route::post('post/delete-post', 'PostController@deletePost');
    Route::post('post/recommend-post', 'PostController@recommendPost');
    Route::post('post/save-post', 'PostController@savePost');
    Route::post('post/unsave-post', 'PostController@unSavePost');
    Route::get('post/detail/{id}', 'PostController@detail');
    Route::get('post/detail-comment/{id}', 'PostController@detailComment');

    Route::get('user/list', 'UserController@list');
    Route::post('user/change-avatar', 'UserController@changeAvatar');
    Route::post('user/update-overview', 'UserController@updateOverview');
    Route::post('create-post', 'PostController@createPost');
    Route::post('create-comment', 'PostController@createComment');

    Route::get('book/detail/{id}', 'BookController@detail');
    Route::post('book/sell-book', 'BookController@sellBook');
    Route::get('book/list', 'BookController@bookList');
    Route::post('shop/create-shop', 'ShopController@createShop');
    Route::get('shop/get-shop', 'ShopController@getShop');

    Route::get('file/{id}', 'PostController@downloadFile');

    Route::get('/auth/logout', 'MeController@logout');

});
