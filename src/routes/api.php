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

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/

//Register
Route::post('/register', function (Request $request) {
    //Validate data
    //TODO: Validate data
    if (!isset($request->email) || !isset($request->password)) {
        return response()->json(['message' => 'Please provide email and password'], 400);
    }

    //Check if user already exists
    $user = DB::table('sl_u_user')->where('u_email', $request->email)->where('u_verified', 1)->first();
    if ($user) {
        return response()->json(['message' => 'User already exists'], 400);
    }

    //Check if the password is valid
    if ($request->password == null || strlen($request->password) < 6 || !is_string($request->password)) {
        return response()->json([
            'error' => 'Password is invalid',
        ], 400);
    }

    //Generate 6 Character long Code containing only numbers.
    $numbers = "0123456789";
    $code = "";
    for ($i = 0; $i < 6; $i++) {
        $code .= $numbers[rand(0, strlen($numbers) - 1)];
    }

    //Check if user already exists but not already verified
    $user = DB::table('sl_u_user')->where('u_email', $request->email)->where('u_verified', 0)->first();
    if ($user) {
        //User already exists. Overwrite Password.
        DB::table('sl_u_user')->where('u_email', $request->email)->update(['u_password' => password_hash($request->password, PASSWORD_DEFAULT), 'u_verificationcode' => $code]);
    }else{
        //Create new user
        DB::table('sl_u_user')->insert(['u_email' => $request->email, 'u_password' => password_hash($request->password, PASSWORD_DEFAULT), 'u_verificationcode' => $code]);
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
    $user = DB::table('sl_u_user')->where('u_email', $request->email)->where('u_verified', 1)->first();

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
    DB::table('sl_t_token')->insert(['t_token' => $token, 't_u_id' => $user->u_id, 't_expiration' => $expiration]);

    //Update last login
    DB::table('sl_u_user')->where('u_id', $user->u_id)->update(['u_lastlogin' => now()->toDateTimeString()]);

    return response()->json([
        'token' => $token,
        'expiration' => $expiration,
    ], 200);
});

//Verify the user
Route::post('/verify', function (Request $request) {
    //Validate data
    if (!isset($request->code) || !isset($request->email)){
        return response()->json(['message' => 'Email or Code is missing'], 400);
    }

    //Get the user from the database
    $user = DB::table('sl_u_user')->where('u_email', $request->email)->where('u_verificationcode', $request->code)->first();

    //Check if the user exists. By checking that you also check if the code is valid
    if (!$user) { 
        return response()->json([
            'error' => 'User not found or code is invalid',
        ], 404);
    }

    //Update the user
    DB::table('sl_u_user')->where('u_id', $user->u_id)->update(['u_verificationcode' => null, 'u_verified' => 1]);

    //Return success
    return response()->json(['message' => 'User verified'], 200);
});

//TODO: Reset Password
Route::post('/resetpwd', function (Request $request) {
    //Validate data
    
});

//Change Password
Route::post('/changepwd', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->password) || !isset($request->newpassword)){
        return response()->json(['message' => 'Token or password is missing'], 400);
    }

    //Get the user from the database
    $user = DB::table('sl_u_user')->join('sl_t_token', 't_u_id', '=', 'u_id')->where('t_token', $request->token)->where('t_expiration', '>', now()->toDateTimeString())->first();

    //Check if the user exists. By checking that you also check if the token is valid
    if (!$user) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Check if the old password is valid
    if (!password_verify($request->password, $user->u_password)) {
        return response()->json([
            'error' => 'Wrong password',
        ], 401);
    }

    //Check if the new Password is a valid password.
    //It has to be at least 6 characters and be a string.
    if ($request->newpassword == null || strlen($request->newpassword) < 6 || !is_string($request->newpassword)) {
        return response()->json([
            'error' => 'Password is invalid',
        ], 400);
    }

    //Update the password
    DB::table('sl_u_user')->where('u_id', $user->u_id)->update(['u_password' => password_hash($request->newpassword, PASSWORD_DEFAULT)]);

    //Return success
    return response()->json(['message' => 'Password changed'], 200);
});

//----------------------------------------------------------------------------------------------------------------------
//List Routes
//----------------------------------------------------------------------------------------------------------------------

//Create List
Route::post('createlist', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->name)){
        return response()->json(['message' => 'Token or name is missing'], 400);
    }

    //Get the user from the database
    $user = DB::table('sl_u_user')->join('sl_t_token', 't_u_id', '=', 'u_id')->where('t_token', $request->token)->where('t_expiration', '>', now()->toDateTimeString())->first();

    //Check if the user exists. By checking that you also check if the token is valid
    if (!$user) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Create UUID
    $uuid = Str::orderedUuid();
    //Create List
    DB::table('sl_l_list')->insert(['l_uuid' => $uuid, 'l_u_id' => $user->u_id, 'l_name' => 'Untitled', 'l_created' => now()->toDateTimeString()]);

    //Return success
    return response()->json(['message' => 'List created'], 200);
});

//Change List Name
Route::post('changelistname', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->uuid) || !isset($request->name)){
        return response()->json(['message' => 'Token, list or name is missing'], 400);
    }

    //Get the user from the database
    $user = DB::table('sl_u_user')->join('sl_t_token', 't_u_id', '=', 'u_id')->where('t_token', $request->token)->where('t_expiration', '>', now()->toDateTimeString())->first();

    //Check if the user exists. By checking that you also check if the token is valid
    if (!$user) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Get the list from the database
    $list = DB::table('sl_l_list')->where('l_uuid', $request->uuid)->where('l_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) { 
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Validate the name
    if ($request->name == null || strlen($request->name) < 1 || !is_string($request->name)) {
        return response()->json([
            'error' => 'Name is invalid',
        ], 400);
    }

    //Update the list
    DB::table('sl_l_list')->where('l_uuid', $request->uuid)->where('l_u_id', $user->u_id)->update(['l_name' => $request->name]);

    //Return success
    return response()->json(['message' => 'List name changed'], 200);
});

//Add User to List
Route::post('invitetolist', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->uuid) || !isset($request->email)){
        return response()->json(['message' => 'Token, list or user is missing'], 400);
    }

    //Get the user from the database
    $user = DB::table('sl_u_user')->join('sl_t_token', 't_u_id', '=', 'u_id')->where('t_token', $request->token)->where('t_expiration', '>', now()->toDateTimeString())->first();

    //Check if the user exists. By checking that you also check if the token is valid
    if (!$user) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Check invited user from the database
    $invitedUser = DB::table('sl_u_user')->where('u_email', $request->email)->where('u_verified', 1)->first();

    //Check if the invited user exists. By checkking that you also check if the user is verified
    if (!$invitedUser) { 
        return response()->json([
            'error' => 'Invited user not found',
        ], 404);
    }

    //Get the list from the database
    $list = DB::table('sl_l_list')->where('l_uuid', $request->uuid)->where('l_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) {
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Check if the user is already in the list
    $userInList = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->uuid)->where('a_u_id', $invitedUser->u_id)->first();

    if ($userInList) {
        return response()->json([
            'error' => 'User is already in the list',
        ], 400);
    }

    //Create UUID
    $uuid = Str::orderedUuid();

    //Create Invite to add user to list
    DB::table('sl_i_invite')->insert(['i_id' => $uuid, 'i_l_id' => $list->l_id, 'i_u_id' => $invitedUser->u_id, 'i_created' => now()->toDateTimeString(), 'i_p_id' => 2]);

    //TODO: Send Invite Email
    

    //Return success
    return response()->json(['message' => 'User invited to list'], 200);
});

