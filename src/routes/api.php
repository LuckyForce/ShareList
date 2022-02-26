<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;

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

//Register
Route::post('/register', function (Request $request) {
    //Validate data
    //TODO: Validate data
    if (!isset($request->email) || !isset($request->password)) {
        return response()->json(['message' => 'Please provide email and password'], 400);
    }

    //Check if user already exists
    $user = DB::select('SELECT * FROM sl_u_users WHERE u_email = ? AND u_emailverified = 1', [$request->email]);
    if (count($user) > 0) {
        return response()->json(['message' => 'User already exists'], 400);
    }

    //Check if user already exists but not already verified
    $user = DB::select('SELECT * FROM sl_u_users WHERE u_email = ? AND u_emailverified = 0', [$request->email]);
    if (count($user) > 0) {
        //User already exists. Overwrite Password.
        DB::update('UPDATE sl_u_users SET u_password = ? WHERE u_email = ?', [password_hash($request->password, PASSWORD_DEFAULT), $request->email]);
    }else{
        //Create new user
        DB::insert('INSERT INTO sl_u_users (u_email, u_password) VALUES (?, ?)', [$request->email, password_hash($request->password, PASSWORD_DEFAULT)]);
    }

    //TODO: Send verification email
    

    //Return success
    return response()->json(['message' => 'User created'], 200);
});

//Login
Route::post('/login', function (Request $request) {
    //Validate data
    if (!isset($request->email) || !isset($request->password)){
        return response()->json(['message' => 'Email or password is missing'], 400);
    }

    //Get the user from the database
    $user = DB::select('SELECT * FROM sl_u_users WHERE u_email = ?', [$request->email])[0];

    //Check if the user exists
    if (!$user) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Check if the Password is valid
    if (!password_verify($request->password, $user->u_password)) {
        return response()->json([
            'error' => 'Wrong password',
        ], 401);
    }

    //Create Token
    $token = Str::orderedUuid();
    $expiration = now()->addMinutes(60*24)->toDateTimeString();
    DB::insert('INSERT INTO sl_u_token (t_token, t_u_id, t_expiration) VALUES (?, ?, ?)', [$token, $user->u_id, $expiration]);

    return response()->json([
        'token' => $token,
        'expiration' => $expiration,
    ], 200);
});

//Verify the user
Route::post('/verify', function (Request $request) {
    //Validate data
    if (!isset($request->code)){
        return response()->json(['message' => 'Code is missing'], 400);
    }

    //Get the user from the database
    $user = DB::select('SELECT * FROM sl_u_users WHERE u_email = ?', [$request->code, $request->email])[0];

    //Check if the user exists. By checking that you also check if the code is valid
    if (!$user) { 
        return response()->json([
            'error' => 'User not found or code is invalid',
        ], 404);
    }

    //Update the user
    DB::update('UPDATE sl_u_users SET u_emailverified = 1 WHERE u_id = ?', [$user->u_id]);
    
    //Delete the code
    DB::update('UPDATE sl_u_users SET u_emailcode = null WHERE u_id = ?', [$user->u_id]);

    //Return success
    return response()->json(['message' => 'User verified'], 200);
});