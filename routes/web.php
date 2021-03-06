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
    return view('welcome');
});

Route::get('/raffle', 'RaffleController@index');
Route::get('/list', 'RaffleController@list');
Route::get('/selected', 'RaffleController@selected');
Route::get('/restart', 'RaffleController@restart');

Route::post('/raffle', 'RaffleController@store');
Route::post('/roll', 'RaffleController@select');


Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/notes/list', 'NoteController@dashboard');
Route::get('/users/list', 'UserController@dashboard');
Route::resource('users', 'UserController');
Route::resource('notes', 'NoteController');
