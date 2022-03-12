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
Route::post('/user/register', function (Request $request) {
    //Validate data
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

    //Check if email is valid


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

//Delete user
Route::post('/user/delete', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->password)) {
        return response()->json(['message' => 'Token or password is missing'], 400);
    }

    //Get user
    $user = getUser($request->token);

    //Check if user exists
    if (!$user) {
        return response()->json(['message' => 'User does not exist'], 400);
    }

    //Check if password is correct
    if (!password_verify($request->password, $user->u_password)) {
        return response()->json(['message' => 'Password is incorrect'], 400);
    }

    //Transfer Ownership of the lists to another user
    $lists = DB::table('sl_l_list')->where('l_u_user', $user->u_id)->get();
    //Get from each list the next user who has access to the list
    foreach ($lists as $list) {
        $nextUser = DB::table('sl_l_list')->join('sl_a_access', 'l_id', '=', 'a_l_id')->where('l_id', $list->l_id)->where('a_u_id', "!=", $user->u_id)->orderBy('a_p_id', 'asc')->first();
        if ($nextUser) {
            //Update the list
            DB::table('sl_l_list')->where('l_id', $list->l_id)->update(['l_u_id' => $nextUser->a_u_id]);
        }else{
            //Delete the list
            DB::table('sl_l_list')->where('l_id', $list->l_id)->delete();
        }
    }

    //Delete user
    DB::table('sl_u_user')->where('u_id', $user->u_id)->delete();

    //Return success
    return response()->json(['message' => 'User deleted'], 200);
});

//Login
Route::post('/user/login', function (Request $request) {
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
Route::post('/user/verify', function (Request $request) {
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
Route::post('/user/resetpwd', function (Request $request) {
    //Validate data
    
});

//Forgot Password
/*
This Function generates a code and sends it to the user per mail.
@param email
@return success
*/
Route::post('/user/forgotpwd', function (Request $request) {
    //Validate data
    if (!isset($request->email)){
        return response()->json(['message' => 'Email is missing'], 400);
    }

    //Get the user from the database
    $user = DB::table('sl_u_user')->where('u_email', $request->email)->where('u_verified', 1)->first();

    //Check if the user exists
    if (!$user) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Generate 6 Character long Code containing only numbers.
    $numbers = "0123456789";
    $code = "";
    for ($i = 0; $i < 6; $i++) {
        $code .= $numbers[rand(0, strlen($numbers) - 1)];
    }

    //Update the user
    DB::table('sl_u_user')->where('u_id', $user->u_id)->update(['u_resetpwd' => $code, 'u_resetpwdexpirationdate' => now()->addMinutes(60*24)->toDateTimeString()]);

    //TODO: Send mail to user


    //Return success
    return response()->json(['message' => 'Code sent'], 200);
});

//Change Password
Route::post('/user/changepwd', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->password) || !isset($request->newpassword)){
        return response()->json(['message' => 'Token or password is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

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
Route::post('/list/create', function (Request $request) {
    //Validate data
    if (!isset($request->token)){
        return response()->json(['message' => 'Token is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);
    
    //Create UUID
    $uuid = Str::orderedUuid();

    //Create List
    DB::table('sl_l_list')->insert(['l_id' => $uuid, 'l_u_id' => $user->u_id, 'l_name' => 'Untitled', 'l_created' => now()->toDateTimeString()]);

    //Add user to list
    DB::table('sl_a_access')->insert(['a_l_id' => $uuid, 'a_u_id' => $user->u_id, 'a_p_id' => 1]);

    //Return success
    return response()->json(['message' => 'List created'], 200);
});

//Get Lists
/*
This Function returns all lists the user has access to.
@param token
@return lists
*/
Route::post('/lists', function (Request $request) {
    //Validate data
    if (!isset($request->token)){
        return response()->json(['message' => 'Token is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Get all lists the user has access to
    $lists = DB::table('sl_l_list')->join('sl_a_access', 'sl_l_list.l_id', '=', 'sl_a_access.a_l_id')->where('sl_a_access.a_u_id', $user->u_id)->get();

    //Return success
    return response()->json(['lists' => $lists], 200);
});

//Get List. Needs to have access to the list
/*
@param string token
@param string list
@return json list
@return json items
@return json admin
*/
Route::post('/list', function (Request $request) {
    //Validate data
    if (!isset($request->token ) || !isset($request->list)){
        return response()->json(['message' => 'Token or list is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Get the list
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->first();

    //Check if the list exists
    if (!$list) { 
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }
    
    //Check if the user has access to the list
    if (!DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $user->u_id)->first()) {
        return response()->json([
            'error' => 'User has no access to this list',
        ], 401);
    }

    //Get items
    $items = DB::table('sl_i_item')->where('i_l_id', $list->l_id)->get();

    //Check if the user is owner of the list.
    if($user->u_id == $list->l_u_id) $admin = true; else $admin = false;

    //Check if the user has write permission for the list.
    $write = DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $user->u_id)->where('a_p_id', 2)->first();
    if($write) $write = true; else $write = false;
    
    //Return list
    return response()->json(['list' => $list, 'items' => $items, 'admin' => $admin, 'write' => $write], 200);
});


//Delete List. Needs to be owner of the list
/*
@param string token
@param string list
@return json success
*/
Route::post('/list/delete', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list)){
        return response()->json(['message' => 'Token or list is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);
    
    //Get the list
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $user->u_id)->first();
    
    //Check if the list exists
    if (!$list) { 
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Delete list
    DB::table('sl_l_list')->where('l_id', $list->l_id)->delete();

    //Return success
    return response()->json(['message' => 'List deleted'], 200);
});

//Change List Name
Route::post('/list/rename', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->name)){
        return response()->json(['message' => 'Token, list or name is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Get the list from the database
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $user->u_id)->first();

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
    DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $user->u_id)->update(['l_name' => $request->name]);
    
    //Return success
    return response()->json(['message' => 'List name changed'], 200);
});

//TODO: Transfer rights for list to another user. Has to be owner of the list
/*
@param string token
@param string list
@param string user
@return json success
*/
Route::post('/list/transfer', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->user) || !isset($request->password)){
        return response()->json(['message' => 'Token, list, user or password is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Check if password is valid
    if (!password_verify($request->password, $user->u_password)) {
        return response()->json([
            'error' => 'Password is invalid',
        ], 401);
    }

    //Get the list from the database
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) { 
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Get the user to transfer to
    $userToTransfer = DB::table('sl_u_user')->where('u_id', $request->user)->first();

    //Check if the user exists
    if (!$userToTransfer) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Check if the user has access to the list
    if (!DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $userToTransfer->u_id)->first()) {
        return response()->json([
            'error' => 'User has no access to this list',
        ], 401);
    }

    //Transfer the list
    DB::table('sl_l_list')->where('l_id', $list->l_id)->update(['l_u_id' => $userToTransfer->u_id]);

    //Remove Access From new Owner and add Access to old Owner
    DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $userToTransfer->u_id)->delete();
    DB::table('sl_a_access')->insert(['a_l_id' => $list->l_id, 'a_u_id' => $user->u_id, 'a_p_id' => 1]);

    //Return success
    return response()->json(['message' => 'List transferred'], 200);
});

//Add User to List
Route::post('/list/invite', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->email)){
        return response()->json(['message' => 'Token, list or user is missing'], 400);
    }

    //Get the user from the database
    $user = getUser($request->token);

    //Check invited user from the database
    $invitedUser = DB::table('sl_u_user')->where('u_email', $request->email)->where('u_verified', 1)->first();

    //Check if the invited user exists. By checkking that you also check if the user is verified
    if (!$invitedUser) { 
        return response()->json([
            'error' => 'Invited user not found',
        ], 404);
    }

    //Get the list from the database
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) {
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Check if the user is already in the list
    $userInList = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $invitedUser->u_id)->first();

    if ($userInList) {
        return response()->json([
            'error' => 'User is already in the list',
        ], 400);
    }

    //Check if the user is already invited
    $userInvited = DB::table('sl_in_invite')->where('i_l_id', $request->list)->where('i_u_id', $invitedUser->u_id)->where('i_accepted', 0)->where('i_deleted', 0)->first();

    if ($userInvited) {
        return response()->json([
            'error' => 'User is already invited',
        ], 400);
    }

    //Create UUID
    $uuid = Str::orderedUuid();

    //Create Invite to add user to list
    DB::table('sl_in_invite')->insert(['in_id' => $uuid, 'in_l_id' => $list->l_id, 'in_u_id' => $invitedUser->u_id, 'in_created' => now()->toDateTimeString(), 'in_p_id' => 2]);

    //TODO: Send Invite Email
    

    //Return success
    return response()->json(['message' => 'User invited to list'], 200);
});

//TODO: Get unnaccepted invites. Needs to be owner of the list
/*
@param string token
@param string list
@return json invites
*/
Route::post('/list/invites', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list)){
        return response()->json(['message' => 'Token or list is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Get the list
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) { 
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Get the invites
    $invites = DB::table('sl_in_invite')->where('in_l_id', $list->l_id)->where('in_accepted', 0)->where('in_deleted', 0)->get();

    //Return success
    return response()->json(['invites' => $invites], 200);
});

//TODO: Delete Invite. Needs to be owner of the list
/*
@param string token
@param string list
@param string invite
@param string user
@return json success
*/
Route::post('/list/invite/delete', function (Request $request) {
});

//TODO: Accept Invite. Needs to be owner of the list.
/*
@param string token
@param string invite
@return json success
*/
Route::post('/list/invite/accept', function (Request $request) {

});

//TODO: Get Members of list.
/*
@param string token
@param string list
@return json members
*/
Route::post('/list/members', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list)){
        return response()->json(['message' => 'Token or list is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Get the list
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) { 
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Get the members
    $members = DB::table('sl_a_access')->where('a_l_id', $list->l_id)->get();

    //Return success
    return response()->json(['members' => $members], 200);
});

//TODO: Remove User from list. Has to be owner of the list
/*
@param string token
@param string list
@param string user
@return json success
*/
Route::post('/list/member/remove', function (Request $request) {

});

//TODO: Change rights for user on list. Has to be owner of the list
/*
@param string token
@param string list
@param string user
@param string rights
@return json success
*/
Route::post('/list/member/rights', function (Request $request) {

});

//Remove Item. Needs to have access to the list
Route::post('/list/item/remove', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->item)){
        return response()->json(['message' => 'Token, list or item is missing'], 400);
    }

    //Get the user from the database
    $user = getUser($request->token);

    //Get the list from the database and check if the user has access to the list
    $list = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) { 
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Get the item from the database
    $item = DB::table('sl_i_item')->where('i_id', $request->item)->where('i_l_id', $list->l_id)->first();

    //Check if the item exists. By checking that you also check if the user is the owner of the list
    if (!$item) { 
        return response()->json([
            'error' => 'Item not found',
        ], 404);
    }

    //Delete the item
    DB::table('sl_i_item')->where('i_id', $request->item)->where('i_l_id', $list->l_id)->delete();

    //Return success
    return response()->json(['message' => 'Item removed'], 200);
});

//Edit Item. Needs to have access to the list
Route::post('/list/item/edit', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->item) || !isset($request->content)){
        return response()->json(['message' => 'Token, list, item or name is missing'], 400);
    }

    //Get the user from the database
    $user = getUser($request->token);

    //Get the list from the database and check if the user has access to the list
    $list = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) { 
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Get the item from the database
    $item = DB::table('sl_i_item')->where('i_id', $request->item)->where('i_l_id', $list->l_id)->first();

    //Check if the item exists.
    if (!$item) { 
        return response()->json([
            'error' => 'Item not found',
        ], 404);
    }

    //Update the item
    DB::table('sl_i_item')->where('i_id', $request->item)->where('i_l_id', $list->l_id)->update(['i_content' => $request->content]);

    //Return success
    return response()->json(['message' => 'Item updated'], 200);
});

//Add Item. Needs to have access to the list
Route::post('/list/item/add', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->content)){
        return response()->json(['message' => 'Token, list or item is missing'], 400);
    }

    //Get the user from the database
    $user = getUser($request->token);

    //Get the list from the database and check if the user has access to the list
    $list = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) {
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Check if content is string and at least one character
    if ($request->content == null || strlen($request->content) < 1 || !is_string($request->content)) {
        return response()->json([
            'error' => 'Name is invalid',
        ], 400);
    }

    //Create UUID
    $uuid = Str::orderedUuid();

    //Create Item
    DB::table('sl_i_item')->insert(['i_id' => $uuid, 'i_l_id' => $list->l_id, 'i_content' => $request->content, 'i_lastupdated' => now()->toDateTimeString()]);

    //Return success
    return response()->json(['message' => 'Item added'], 200);
});

//Check/Uncheck Item. Needs to have access to the list
Route::post('/list/item/check', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->item)){
        return response()->json(['message' => 'Token, list or item is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Get the list from the database and check if the user has access to the list
    $list = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $user->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) {
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Get the item from the database
    $item = DB::table('sl_i_item')->where('i_id', $request->item)->where('i_l_id', $list->l_id)->first();

    //Check if the item exists. By checking that you also check if the user is the owner of the list
    if (!$item) {
        return response()->json([
            'error' => 'Item not found',
        ], 404);
    }

    //Update the item
    DB::table('sl_i_item')->where('i_id', $request->item)->where('i_l_id', $list->l_id)->update(['i_checked' => !$item->i_checked]);

    //Return success
    return response()->json(['message' => 'Item checked'], 200);
});

function getUser($token) {
    //Get user from database
    $user = DB::table('sl_u_user')->join('sl_t_token', 't_u_id', '=', 'u_id')->where('t_token', $token)->where('t_expiration', '>', now()->toDateTimeString())->first();
    if (!$user) { 
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }
    return $user;
}