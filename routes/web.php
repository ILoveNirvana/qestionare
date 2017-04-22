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

Route::get('/', function () {
	return view('index');
});

Route::get('/question/{id}', 'QuestionController@get');

Route::post('/question/check/{id}', 'QuestionController@check');


// TESTS API Going there

Route::get('tests/', 'TestsController@getAll');

Route::get('test/{id}', 'TestsController@get');


Route::put('test/', 'TestsController@add');


Route::delete('test/{id}', 'TestsController@remove');

Route::patch('test/{id}/check', 'TestsController@check');