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

Route::get('avatar/{id}', function($id)
{
    return Image::make(public_path('/avatar/'.$id))->response('png');
});

Route::group(['middleware' => 'jwt.auth'], function() {
    Route::get('account/{id}', 'UserController@account');
    Route::get('user/list', 'UserController@list');
    Route::get('/me', 'MeController@index');
    Route::get('post/list', 'PostController@list');
    Route::get('post/detail/{id}', 'PostController@detail');

    Route::get('post/detail/{id}', 'PostController@detail');

    Route::post('update-overview', 'UserController@updateOverview');



    Route::get('/auth/logout', 'MeController@logout');

});
