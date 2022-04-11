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
Route::get('', function () {
    return view('page');
});
/*
if(request()->isMethod('get')){
    $matched = false;
    while(!$matched){
        $path = '/{any?}';
        Route::get($path, function() {
            $matched = true;
            return view('page');
        });
        $path .= '/{any?}';
    }
}
/*
Route::get('/{any}', function () {
    return view('page');
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

Route::get('/list/{id}/invites', function ($id) {
    return view('invite', ['id' => $id]);
});

Route::get('/list/{id}/member', function ($id) {
    return view('member', ['id' => $id]);
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
*/