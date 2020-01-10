<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group(['middleware' => ['auth']],function(){

    // User Controller
    Route::resource('profile', 'UserController');
    Route::post('/profile/update/password', 'UserController@updatePassword');
    Route::post('/profile/config/update', 'UserController@updateConfig');    
    Route::post('/profile/upload/{id}', 'UserController@uploadProfilePic')->middleware('optimizeImages');
    Route::post('/profile/upload/terms-policies/tmp', 'UserController@uploadConfigFile')->middleware('optimizeImages');
    Route::get('/search/email/{name}', 'UserController@seacrhEmail');

    // Read Controller
    Route::get('/user/recently/view', 'ReadController@getUserRecentView');
    Route::get('/recently/viewed/story', 'ReadController@getTopRecentView');

    // Address Route
    Route::resource('/profile/address', 'AddressController');

    // Read Controller
    Route::get('read/pagination/{id}/{type}', 'ReadController@getNextAndPrev')->name('read');
    Route::get('read/{id}/stories', 'ReadController@getStories')->name('read');
    Route::get('read/{id}/books', 'ReadController@getBooks')->name('read');
    
    
    // Story Controller
    Route::resource('/write', 'StoryController');
    Route::post('/stories', 'StoryController@getStories');
    Route::post('/story/list', 'StoryController@getStoriesByDate');
    Route::post('/story/upload/{id}', 'StoryController@upload')->middleware('optimizeImages');
    Route::get('/story/page/delete/{id}', 'StoryController@deletePage');
    Route::post('/story/add/pdf', 'StoryController@uploadPDF');

    // Home Controller
    Route::get('/print/book/{id}','HomeController@getData');
    Route::post('/print/calculate','HomeController@getCalculate');
	Route::post('/print/checkout', 'HomeController@checkOutPrint');
    Route::get('/mystorybook','HomeController@home')->name('mystorybook');

    // Book Controller
    Route::resource('/book','BookController');
    Route::post('/books', 'BookController@getBooks');
    Route::post('/book/cover/{id}','BookController@cover');
    Route::post('/book/upload/{id}/{name}', 'BookController@uploadFile')->middleware('optimizeImages');
});

Route::get('/verify-user/{code}', 'Auth\RegisterController@activateUser')->name('activate.user');

Route::get('/pdf/{id}','DownloadController@getPDF');
Route::get('/epub/{id}','DownloadController@getEpub');

Route::get('/','IndexController@index')->name('index');
Route::get('/terms-policies','IndexController@terms')->name('terms-policies');

Route::resource('/cart','CartController');
Route::post('/checkout', 'CartController@checkOutOrder');

Route::post('/book/publish', 'CartController@getPublishBooks');

Auth::routes();
