<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::controller( 'form', 'FormController');
Route::controller( 'user', 'UserController');
//Route::get('/show', 'FormController@show');


Route::get('/', function()
{
	return View::make('index');
});

Route::get('/reports', function()
{
	return View::make('reports');
});

