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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});



//ArticleController
Route::get('/article/{id}','ArticleController@getArticleData');
Route::get('/articles/{category}','ArticleController@articlesByCategory'); 
Route::get('/articles/search/{keyword}','ArticleController@searchArticle');

Route::post('/article/delete','ArticleController@delete'); 
Route::post('/article/add','ArticleController@add'); 


//CommentsController
Route::get('/article/{id}/comments','CommentsController@getArticleComments');

//UserController
Route::post('/user/register','UserController@register');
Route::post('/user/login','UserController@login');