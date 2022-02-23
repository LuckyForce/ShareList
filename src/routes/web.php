<?php

use Illuminate\Support\Facades\Route;

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

//TODO: Delete
Route::get('/', function () {
    return view('welcome');
});

//TODO: Change to /
Route::get('/homepage', function () {
    return view('homepage');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/contact', function () {
    return view('contact');
});

Route::get('/lists', function () {
    return view('lists');
});

Route::get('/list/{id}', function ($id) {
    return view('lists', ['id' => $id]);
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/user', function ($id) {
    return view('user', ['id' => $id]);
});
