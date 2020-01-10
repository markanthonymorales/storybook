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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::post('/profile/upload/{id}', 'UserController@uploadFile');
// Route::post('/profile/update/info', 'UserController@update');
// Route::post('/profile/update/password', 'UserController@updatePassword');

Route::group(['middleware' => ['json.response']], function () {

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    // public routes
    // Route::post('/profile/upload/{id}', 'UserController@uploadFile')->name('upload');

    // private routes
    Route::middleware('auth:api')->group(function () {
      
    });

});