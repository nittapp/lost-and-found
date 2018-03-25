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

Route::get('/', 'UserController@renderDashboard');
Route::get('/items','UserController@renderUserItems');
Route::get('/item','UserController@renderCreateView');
Route::get('/item/{id}/comment','UserController@renderCommentView');
Route::get('/item/{id}','UserController@renderEditView');
Route::post('/item','UserController@createItem');
Route::post('/item/{id}','UserController@editItem');
Route::delete('/item/{id}','UserController@deleteItem');
Route::delete('/comment/{id}','UserController@deleteComment');
Route::post('/comment/{id}','UserController@createComment');






// storage files
Route::get('/images/{dir}/{filename}', function ($dir, $filename)
{   
    $path = storage_path().'/app/'.$dir.'/'.$filename;
    if(!File::exists($path)) abort(404);

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
});