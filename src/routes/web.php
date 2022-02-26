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

Route::get('/', function () {
    return view('homepage');
});

Route::get('/about', function () {
    return view('about');
});

Route::get('/lists', function () {
    return view('lists');
});

Route::get('/list/{id}', function ($id) {
    return view('list', ['id' => $id]);
});

Route::get('/list/{id}/', function ($id) {
    return view('access', ['id' => $id]);
});

Route::get('/register', function () {
    return view('register');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/user', function () {
    return view('user');
});
