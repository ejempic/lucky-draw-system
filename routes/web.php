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

Route::get('/draw', 'DrawController@draw')->name('draw');
Route::get('/save_number', 'DrawController@save_number')->name('save_number');
Route::get('/generate_random_user', 'HomeController@generate_random_user')->name('generate_random_user');

Auth::routes();

Route::get('/', 'HomeController@welcome')->name('/');
Route::get('/home', 'HomeController@index')->name('home');
Route::get('/truncate_winners', 'HomeController@truncate_winners')->name('truncate_winners');
Route::get('/ajax_winners', 'HomeController@ajax_winners')->name('ajax_winners');
Route::get('/ajax_members', 'HomeController@ajax_members')->name('ajax_members');
