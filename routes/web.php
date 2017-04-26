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
	return view('app');
});


// TESTS API Going there

Route::get('tests/', 'TestsController@getAll');

Route::get('test/{id}', 'TestsController@get');

Route::options('/seacrh/tests/{query}', 'TestsController@seacrhingTests');


Route::put('test/', 'TestsController@add');


Route::delete('test/{id}', 'TestsController@remove');

Route::patch('test/{id}/check', 'TestsController@check');