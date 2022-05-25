<?php
//display server error
ini_set('display_errors', 1);

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Jobs\SendEmail;


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

//TODO: FOOTER:
const FOOTER = "© 2020 - All rights reserved";

//Register
Route::post('/user/register', function (Request $request) {
    //Validate data
    if (!isset($request->email) || !isset($request->password)) {
        return response()->json(['error' => 'Please provide email and password'], 400);
    }

    //Make Email Lowercase
    $email = mb_strtolower($request->email);

    //Check if the password is valid
    if ($request->password == null || !is_string($request->password) || strlen($request->password) < 6 || strlen($request->password) > 20) {
        return response()->json([
            'error' => 'Password is invalid',
        ], 400);
    }

    //Check if email is valid
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json([
            'error' => 'Email is invalid',
        ], 400);
    }

    //Check if user already exists
    $user = DB::table('sl_u_user')->where('u_email', $email)->where('u_verified', 1)->first();
    if ($user) {
        return response()->json(['error' => 'User already exists'], 400);
    }

    //Generate 6 Character long Code containing only numbers.
    $numbers = "0123456789";
    $code = "";
    for ($i = 0; $i < 6; $i++) {
        $code .= $numbers[rand(0, strlen($numbers) - 1)];
    }

    //Check if user already exists but not already verified
    $user = DB::table('sl_u_user')->where('u_email', $email)->where('u_verified', 0)->first();
    if ($user) {
        //User already exists. Overwrite Password.
        DB::table('sl_u_user')->where('u_email', $email)->update(['u_password' => password_hash($request->password, PASSWORD_DEFAULT), 'u_verifytoken' => $code]);
    } else {
        //Create new user
        DB::table('sl_u_user')->insert(['u_email' => $email, 'u_password' => password_hash($request->password, PASSWORD_DEFAULT), 'u_verifytoken' => $code]);
    }

    //Get User ID
    $user = DB::table('sl_u_user')->where('u_email', $email)->first();

    //TODO: Design verification email
    $link = url('/verify' . '/' . $user->u_id . '/' . $code);

    //Create Email
    $recipient = $user->u_email;
    $subject = 'Verify your email';
    $body = '<p style="text-align:center; font-size: 40px;">Please click on the following link to verify your email address:</p>
    <p style="text-align:center;"><a href="' . $link . '" style="font-size: 50px;">Click Here</a></p>' . FOOTER;
    $altBody = 'Please click on the following link to verify your email address: ' . $link;

    //Create Email
    $mail = new SendEmail($recipient, $subject, $body, $altBody);

    //Dispatch Email
    dispatch($mail);

    //Return success
    return response()->json(['message' => 'User created'], 200);
});

//Delete user
Route::post('/user/delete', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->password)) {
        return response()->json(['error' => 'Token or password is missing'], 400);
    }

    //Get user
    $user = getUser($request->token);

    //Check if user exists
    if (!$user) {
        return response()->json(['error' => 'User does not exist'], 400);
    }

    //Check if password is correct
    if (!password_verify($request->password, $user->u_password)) {
        return response()->json(['error' => 'Password is incorrect'], 401);
    }

    //Get all lists where user is owner
    $lists = DB::table('sl_l_list')->where('l_u_id', $user->u_id)->get();

    //Delete all accesses to lists
    foreach ($lists as $list) {
        DB::table('sl_a_access')->where('a_l_id', $list->l_id)->delete();
    }

    //Delete all lists where user is owner.
    DB::table('sl_l_list')->where('l_u_id', $user->u_id)->delete();

    //Delete all accesses to user.
    DB::table('sl_a_access')->where('a_u_id', $user->u_id)->delete();

    //Delete all invites from user.
    DB::table('sl_in_invite')->where('in_u_id', $user->u_id)->orWhere('in_invitedby', $user->u_id)->delete();

    //Delete all tokens from user.
    DB::table('sl_t_token')->where('t_u_id', $user->u_id)->delete();

    //Delete user
    DB::table('sl_u_user')->where('u_id', $user->u_id)->delete();

    //Return success
    return response()->json(['message' => 'User deleted'], 200);
});

//Login
Route::post('/user/login', function (Request $request) {
    //Validate data
    if (!isset($request->email) || !isset($request->password)) {
        return response()->json(['error' => 'Email or password is missing'], 400);
    }

    //Make Email Lowercase
    $email = mb_strtolower($request->email);

    //Get the user from the database
    $user = DB::table('sl_u_user')->where('u_email', $email)->where('u_verified', 1)->first();

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

    //Delete all tokens that are invalid (t_expires older than now)
    DB::table('sl_t_token')->where('t_expires', '<', now()->toDateTimeString())->delete();

    //Create Token
    $token = Str::orderedUuid();
    $expires = now()->addMinutes(60 * 24)->toDateTimeString();
    DB::table('sl_t_token')->insert(['t_token' => $token, 't_u_id' => $user->u_id, 't_expires' => $expires]);

    //Update last login
    DB::table('sl_u_user')->where('u_id', $user->u_id)->update(['u_lastlogin' => now()->toDateTimeString()]);

    return response()->json([
        'token' => $token,
        'expires' => $expires,
    ], 200);
});

//Verify the user
/*
This function verifies the user.
It is called when the user clicks on the verification link in the email.
@param $token: The token that is sent to the user.
@return 200 if the user is verified.
*/
Route::post('/user/verify', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->id)) {
        return response()->json(['error' => 'Token is missing'], 400);
    }

    //Get the user from the database
    $user = DB::table('sl_u_user')->where('u_id', $request->id)->where('u_verifytoken', $request->token)->first();

    //Check if the user exists
    if (!$user) {
        return response()->json([
            'error' => 'Code is invalid',
        ], 400);
    }

    //Check if the user is already verified
    if ($user->u_verified == 1) {
        return response()->json([
            'error' => 'User is already verified',
        ], 400);
    }

    //Verify the user
    DB::table('sl_u_user')->where('u_id', $user->u_id)->update(['u_verified' => 1]);

    //Return success
    return response()->json(['message' => 'User verified'], 200);
});

//Check if User is verified
/*
This function checks if the user is verified.
@param $email: The email of the user.
@return 200 if the user is verified.
*/
Route::post('/user/check', function (Request $request) {
    //Validate data
    if (!isset($request->email)) {
        return response()->json(['error' => 'Email is missing'], 400);
    }

    //Make Email Lowercase
    $email = mb_strtolower($request->email);

    //Get the user from the database
    $user = DB::table('sl_u_user')->where('u_email', $email)->where('u_verified', 1)->exists();

    //Check if the user exists
    if (!$user) {
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Return success
    return response()->json(['message' => 'User verified'], 200);
});

//Email in use
/*
This function checks if the email is in use.
@param $email: The email of the user.
@return 200 if the email is not in use.
*/
Route::post('/user/checkemail', function (Request $request) {
    //Validate data
    if (!isset($request->email)) {
        return response()->json(['error' => 'Email is missing'], 400);
    }

    //Make Email Lowercase
    $email = mb_strtolower($request->email);

    //Get the user from the database
    $user = DB::table('sl_u_user')->where('u_email', $email)->where('u_verified', 1)->exists();

    //Check if the user exists
    if ($user) {
        return response()->json([
            'error' => 'Email is already in use',
        ], 400);
    }

    //Return success
    return response()->json(['message' => 'Email is not in use'], 200);
});

//OPTIONAL: Reset Password
Route::post('/user/resetpwd', function (Request $request) {
    //Validate data

});

//OPTIONAL: Forgot Password
/*
This Function generates a code and sends it to the user per mail.
@param email
@return success
*/
Route::post('/user/forgotpwd', function (Request $request) {
    //Validate data
    if (!isset($request->email)) {
        return response()->json(['error' => 'Email is missing'], 400);
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
    DB::table('sl_u_user')->where('u_id', $user->u_id)->update(['u_resetpwd' => $code, 'u_resetpwdexpirationdate' => now()->addMinutes(60 * 24)->toDateTimeString()]);

    //TODO: Send mail to user


    //Return success
    return response()->json(['message' => 'Code sent'], 200);
});

//Change Password
/*
This function changes the password of the user.
@param $token: The token that is sent to the user.
@param $password: The old password.
@param $newpassword: The new password.
@return 200 if the password is changed.
*/
Route::post('/user/changepwd', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->oldPassword) || !isset($request->newPassword)) {
        return response()->json(['error' => 'Token or password is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Check if the old password is valid
    if (!password_verify($request->oldPassword, $user->u_password)) {
        return response()->json([
            'error' => 'Wrong password',
        ], 401);
    }

    //Check if the new Password is a valid password.
    //It has to be at least 6 characters and be a string.
    if ($request->newPassword == null || strlen($request->newPassword) < 6 || !is_string($request->newPassword)) {
        return response()->json([
            'error' => 'Password is invalid',
        ], 400);
    }

    //Update the password
    DB::table('sl_u_user')->where('u_id', $user->u_id)->update(['u_password' => password_hash($request->newPassword, PASSWORD_DEFAULT)]);

    //Return success
    return response()->json(['message' => 'Password changed'], 200);
});

//----------------------------------------------------------------------------------------------------------------------
//List Routes
//----------------------------------------------------------------------------------------------------------------------

//Create List
Route::post('/list/create', function (Request $request) {
    //Validate data
    if (!isset($request->token)) {
        return response()->json(['error' => 'Token is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Create UUID
    $uuid = Str::orderedUuid();

    //Create List
    DB::table('sl_l_list')->insert(['l_id' => $uuid, 'l_u_id' => $user->u_id, 'l_name' => 'Untitled', 'l_description' => 'Untitled', 'l_created' => now()->toDateTimeString()]);

    //Add user to list
    DB::table('sl_a_access')->insert(['a_l_id' => $uuid, 'a_u_id' => $user->u_id, 'a_write' => 1]);

    //Return success with list id
    return response()->json(['message' => 'List created', 'list' => $uuid], 200);
});

//Get Lists
/*
This Function returns all lists the user has access to.
@param token
@return lists
*/
Route::post('/lists', function (Request $request) {
    //Validate data
    if (!isset($request->token)) {
        return response()->json(['error' => 'Token is missing'], 400);
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
    if (!isset($request->token) || !isset($request->list)) {
        return response()->json(['error' => 'Token or list is missing'], 400);
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
    if ($user->u_id == $list->l_u_id) $admin = true;
    else $admin = false;

    //Check if the user has write permission for the list.
    $write = DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $user->u_id)->where('a_write', 1)->first();
    if ($write) $write = true;
    else $write = false;

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
    if (!isset($request->token) || !isset($request->list)) {
        return response()->json(['error' => 'Token or list is missing'], 400);
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

//Update List Name and Description. Needs to be owner of the list
Route::post('/list/update', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->name) || !isset($request->description)) {
        return response()->json(['error' => 'Token, list, name or description is missing'], 400);
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

    //Validate the name. Max 20 characters
    if ($request->name == null || strlen($request->name) < 1 || !is_string($request->name) || strlen($request->name) > 20) {
        return response()->json([
            'error' => 'Name is invalid',
        ], 400);
    }

    //Validate the description. Max 255 characters
    if ($request->description == null || strlen($request->description) < 1 || !is_string($request->description) || strlen($request->description) > 255) {
        return response()->json([
            'error' => 'Description is invalid',
        ], 400);
    }

    //Update the list
    DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $user->u_id)->update(['l_name' => $request->name, 'l_description' => $request->description]);

    //Return success
    return response()->json(['message' => 'List name changed'], 200);
});

//Transfer rights for list to another user. Has to be owner of the list
/*
@param string token
@param string list
@param string user
@return json success
*/
Route::post('/list/transfer', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->user) || !isset($request->password)) {
        return response()->json(['error' => 'Token, list, user or password is missing'], 400);
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
    DB::table('sl_a_access')->insert(['a_l_id' => $list->l_id, 'a_u_id' => $user->u_id, 'a_write' => 1]);

    //Return success
    return response()->json(['message' => 'List transferred'], 200);
});

//Add User to List
Route::post('/list/invite', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->email)) {
        return response()->json(['error' => 'Token, list or user is missing'], 400);
    }

    //Make email lowercase
    $email = strtolower($request->email);

    //Get the user from the database
    $user = getUser($request->token);

    //Check invited user from the database
    $invitedUser = DB::table('sl_u_user')->where('u_email', $email)->where('u_verified', 1)->first();

    //Check if the invited user exists. By checking that you also check if the user is verified
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
    $userInvited = DB::table('sl_in_invite')->where('in_l_id', $request->list)->where('in_u_id', $invitedUser->u_id)->where('in_accepted', 0)->where('in_deleted', 0)->first();

    if ($userInvited) {
        return response()->json([
            'error' => 'User is already invited',
        ], 400);
    }

    //Create UUID
    $uuid = Str::orderedUuid();

    //Create Invite to add user to list
    DB::table('sl_in_invite')->insert(['in_id' => $uuid, 'in_l_id' => $list->l_id, 'in_u_id' => $invitedUser->u_id, 'in_invitedby' => $user->u_id, 'in_accepted' => 0, 'in_deleted' => 0, 'in_created' => now()->toDateTimeString()]);

    //TODO: Design Email
    //Send Invite Email
    $link = url('/invite' . '/' . $uuid);

    //Create Email
    $recipient = $invitedUser->u_email;
    $subject = 'Invite to ' . $list->l_name . ' on ShareList';
    $body = '<p>Please click on the following link to accept the invite:</p>
    <p><a href="' . $link . '">' . $link . '</a></p>' . FOOTER;
    $altBody = 'Please click on the following link to accept the invite: ' . $link;

    //Create Email
    $mail = new SendEmail($recipient, $subject, $body, $altBody);

    //Dispatch Email
    dispatch($mail);

    //Return success
    return response()->json(['message' => 'User invited to list'], 200);
});

//Get unnaccepted invites. Needs to be owner of the list
/*
@param string token
@param string list
@return json invites
*/
Route::post('/list/invites', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list)) {
        return response()->json(['error' => 'Token or list is missing'], 400);
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

//Delete Invite. Needs to be owner of the list
/*
@param string token
@param string list
@param string invite
@param string user
@return json success
*/
Route::post('/list/invite/delete', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->invite)) {
        return response()->json(['error' => 'Token or invite is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Get the invite
    $invite = DB::table('sl_in_invite')->where('in_id', $request->invite)->where('in_accepted', 0)->where('in_deleted', 0)->first();

    //Check if the invite exists.
    if (!$invite) {
        return response()->json([
            'error' => 'Invite not found',
        ], 404);
    }

    //Check if the user is the owner of the list that corresponds to the invite
    $list = DB::table('sl_l_list')->where('l_id', $invite->in_l_id)->where('l_u_id', $user->u_id)->first();

    if (!$list) {
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Delete invite
    DB::table('sl_in_invite')->where('in_id', $invite->in_id)->update(['in_deleted' => 1]);

    //Return success
    return response()->json(['message' => 'Invite deleted'], 200);
});

//Accept Invite.
/*
@param string invite
@return json success
*/
Route::post('/list/invite/accept', function (Request $request) {
    //Validate data
    if (!isset($request->invite)) {
        return response()->json(['error' => 'Invite is missing'], 400);
    }

    //Get the invite
    $invite = DB::table('sl_in_invite')->where('in_id', $request->invite)->first();

    //Check if the invite exists.
    if (!$invite) {
        return response()->json([
            'error' => 'Invite not found',
        ], 404);
    }

    //Check if the invite is deleted
    if ($invite->in_deleted) {
        return response()->json([
            'error' => 'Invite is deleted',
        ], 400);
    }

    //Check if the invite is already accepted
    if ($invite->in_accepted) {
        return response()->json([
            'error' => 'Invite is already accepted',
        ], 400);
    }

    //Accept invite
    DB::table('sl_in_invite')->where('in_id', $invite->in_id)->update(['in_accepted' => 1]);

    //Add User of invite to list
    //Check if user is already in list
    $userInList = DB::table('sl_a_access')->where('a_l_id', $invite->in_l_id)->where('a_u_id', $invite->in_u_id)->first();

    if (!$userInList) {
        DB::table('sl_a_access')->insert(['a_l_id' => $invite->in_l_id, 'a_u_id' => $invite->in_u_id, 'a_write' => 0]);
    }

    //Return success
    return response()->json(['message' => 'Invite accepted'], 200);
});

//Get Members of list.
/*
@param string token
@param string list
@return json members
*/
Route::post('/list/members', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list)) {
        return response()->json(['error' => 'Token or list is missing'], 400);
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

    //Get the members id except the owner
    $membersId = DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', '!=', $user->u_id)->get();

    //get the members email address and id with id
    $members = [];
    foreach ($membersId as $member) {
        $member = DB::table('sl_u_user')->where('u_id', $member->a_u_id)->first();
        array_push($members, ['id' => $member->u_id, 'email' => $member->u_email]);
    }

    //Return success
    return response()->json(['members' => $members], 200);
});

/*
@param string token
@param string list
@param string user
@return json success
*/
Route::post('/list/member/remove', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->user)) {
        return response()->json(['error' => 'Token, list or user is missing'], 400);
    }

    //Get the owner
    $owner = getUser($request->token);

    //Get the list
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $owner->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) {
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Get the user
    $user = DB::table('sl_u_user')->where('u_id', $request->user)->first();

    //Check if the user exists
    if (!$user) {
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Check if the user is already in the list
    $userInList = DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $user->u_id)->first();

    if (!$userInList) {
        return response()->json([
            'error' => 'User is not in the list',
        ], 400);
    }

    //Remove the user from the list
    DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $user->u_id)->delete();

    //Return success
    return response()->json(['message' => 'User removed from list'], 200);
});

//Change rights for user on list. Has to be owner of the list
/*
@param string token
@param string list
@param string user
@param string write
@return json success
*/
Route::post('/list/member/write', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->user) || !isset($request->write)) {
        return response()->json(['error' => 'Token, list, user or rights is missing'], 400);
    }

    //Get the owner
    $owner = getUser($request->token);

    //Get the list
    $list = DB::table('sl_l_list')->where('l_id', $request->list)->where('l_u_id', $owner->u_id)->first();

    //Check if the list exists. By checking that you also check if the user is the owner of the list
    if (!$list) {
        return response()->json([
            'error' => 'List not found',
        ], 404);
    }

    //Get the user
    $user = DB::table('sl_u_user')->where('u_id', $request->user)->first();

    //Check if the user exists
    if (!$user) {
        return response()->json([
            'error' => 'User not found',
        ], 404);
    }

    //Check if the user is already in the list
    $userInList = DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $user->u_id)->first();

    if (!$userInList) {
        return response()->json([
            'error' => 'User is not in the list',
        ], 400);
    }

    //Change the write rights true change true else false
    if ($request->write == 'true') {
        DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $user->u_id)->update(['a_write' => 1]);
    } else {
        DB::table('sl_a_access')->where('a_l_id', $list->l_id)->where('a_u_id', $user->u_id)->update(['a_write' => 0]);
    }
});

//Remove Item. Needs to have access to the list
Route::post('/list/item/delete', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->item)) {
        return response()->json(['error' => 'Token, list or item is missing'], 400);
    }

    //Get the user from the database
    $user = getUser($request->token);

    //Get the list from the database and check if the user has access to the list
    $list = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $user->u_id)->where('a_write', 1)->first();

    //Check if the list exists. By checking that you also check if the user has write access to the list.
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
Route::post('/list/item/update', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->item) || !isset($request->content)) {
        return response()->json(['error' => 'Token, list, item or name is missing'], 400);
    }

    //Get the user from the database
    $user = getUser($request->token);

    //Get the list from the database and check if the user has access to the list
    $list = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $user->u_id)->where('a_write', 1)->first();

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
    DB::table('sl_i_item')->where('i_id', $request->item)->where('i_l_id', $list->l_id)->update(['i_content' => $request->content, 'i_lastupdated' => now()->toDateTimeString()]);

    //Return success
    return response()->json(['message' => 'Item updated'], 200);
});

//Add Item. Needs to have access to the list
Route::post('/list/item/add', function (Request $request) {
    //Validate data
    if (!isset($request->token) || !isset($request->list) || !isset($request->content)) {
        return response()->json(['error' => 'Token, list or item is missing'], 400);
    }

    //Get the user from the database
    $user = getUser($request->token);

    //Get the list from the database and check if the user has access to the list
    $list = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $user->u_id)->where('a_write', 1)->first();

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
    if (!isset($request->token) || !isset($request->list) || !isset($request->item)) {
        return response()->json(['error' => 'Token, list or item is missing'], 400);
    }

    //Get the user
    $user = getUser($request->token);

    //Get the list from the database and check if the user has access to the list
    $list = DB::table('sl_l_list')->join('sl_a_access', 'a_l_id', '=', 'l_id')->where('l_id', $request->list)->where('a_u_id', $user->u_id)->where('a_write', 1)->first();

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

function getUser($token)
{
    $exists = DB::table('sl_u_user')->join('sl_t_token', 't_u_id', '=', 'u_id')->where('t_token', $token)->where('t_expires', '>', now()->toDateTimeString())->exists();
    //Check if User exists
    if (!$exists) {
        response()->json([
            'error' => 'User not found',
        ], 404)->send();
        exit;
    }

    //Get user from database
    $user = DB::table('sl_u_user')->join('sl_t_token', 't_u_id', '=', 'u_id')->where('t_token', $token)->where('t_expires', '>', now()->toDateTimeString())->first();
    return $user;
}
