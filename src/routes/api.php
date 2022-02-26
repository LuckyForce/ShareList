<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/register', function () {
    return User::create([
        'name' => request('name'),
        'email' => request('email'),
        'password' => password_hash(request('password'), PASSWORD_DEFAULT)
    ]);
});

Route::post('/login', function () {
    $user = User::where('email', request('email'))->first();

    if (!$user) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    if (!password_verify(request('password'), $user->password)) {
        return response()->json([
            'error' => 'Wrong password',
        ], 401);
    }

    $token = $user->createToken('Laravel Password Grant Client')->accessToken;

    return response()->json([
        'token' => $token,
    ]);
});