<?php

use Carbon\Carbon;
use App\Models\Food;
use App\Models\Admin;
use App\Models\Follower;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


function admin()
{
    return Auth::guard('admin')->user();
}

function getAdminByID($id)
{
    return Admin::find($id);
}

// Api
function api_user(){
    return Auth::guard('user-api')->user();
}

function getUserByID($user_id){
    $user = User::find($user_id);
    return $user;
}

function short_time($created_at)
{
    $time = Carbon::parse($created_at)->shortRelativeToNowDiffForHumans();
    return str_replace(' ago', '', $time);
}

function short_time_chat($created_at)
{
    $time = Carbon::parse($created_at)->diffForHumans();

    $time = str_replace('minutes', 'min', $time);
    $time = str_replace('minute', 'min', $time);
    $time = str_replace('seconds', 'sec', $time);
    $time = str_replace('second', 'sec', $time);

    return $time;
}

function post_owner_info($user_id, $auth_id)
{
    $user = DB::table('users')->select('id', 'name', 'avatar', 'email_verified_at as is_verified')->find($user_id);
    $user->avatar = url('/') . '/'. $user->avatar;
    $user->is_verified = $user->is_verified ? 1 : 0;

    $follow = Follower::where('user_id', $user_id)->where('follower_id', $auth_id)->first();
    $user->is_following = $follow ? 1 : 0;

    return $user;
}
function post_owner_info_stories($user_id)
{
    $user = DB::table('users')->select('id', 'name', 'avatar', 'email_verified_at as is_verified')->find($user_id);
    $user->avatar = url('/') . '/'. $user->avatar;
    $user->is_verified = $user->is_verified ? 1 : 0;

    return $user;
}

function comment_user_info($user_id)
{
    $user = DB::table('users')->select('id', 'name', 'avatar', 'email_verified_at as is_verified')->find($user_id);
    $user->avatar = url('/') . '/'. $user->avatar;
    $user->is_verified = $user->is_verified ? 1 : 0;
    return $user;
}

function notification($for, $user_id, $notification_text, $type, $post_id = NULL, $comment_id = NULL)
{
    if ($type == 'follow') {
        Notification::where('type', 'follow')->where('user_id', $user_id)->where('notification_for', $for)->delete();
    }
    if ($type == 'like') {
        Notification::where('type', 'like')->where('user_id', $user_id)->where('notification_for', $for)->delete();
    }
    if ($type == 'comment') {
        Notification::where('type', 'comment')->where('user_id', $user_id)->where('notification_for', $for)->delete();
    }
    if ($type == 'comment_reply') {
        Notification::where('type', 'comment_reply')->where('user_id', $user_id)->where('notification_for', $for)->delete();
    }

    if ($for != $user_id) {
        $notification = new Notification();
        $notification->notification_for = $for;
        $notification->user_id = $user_id;
        $notification->post_id = $post_id;
        $notification->comment_id = $comment_id;
        $notification->notification_text = $notification_text;
        $notification->type = $type;
        $notification->save();
    }

}

function uploadFile($file, $folder)
{
    $fileName = uniqid() . Carbon::now()->timestamp. '.' .$file->extension();
    $file->storeAs($folder, $fileName);

    $file_name = 'uploads/'.$folder.'/'.$fileName;
    return $file_name;
}


function adminPermissions()
{
    $permissions = [];
    foreach (json_decode(admin()->permissions) as $permission) {
        $perm = Permission::where('id', $permission)->first()->value;
        $permissions[] = $perm;
    }
    return $permissions;
}

function isAdminPermitted($permission)
{
    $permission = Permission::where('value', $permission)->first();

    if (in_array($permission->id, json_decode(admin()->permissions))) {
        return true;
    } else {
        return false;
    }
}

function loadingStateSm($key, $title)
{
    $loadingSpinner = '
        <div wire:loading wire:target="' . $key . '" wire:key="' . $key . '"><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> loading</div>
        <div wire:loading.remove wire:target="' . $key . '" wire:key="' . $key . '">' . $title . '</div>
    ';

    return $loadingSpinner;
}

function loadingStateXs($key, $title)
{
    $loadingSpinner = '
        <div wire:loading wire:target="' . $key . '" wire:key="' . $key . '"><span class="spinner-border spinner-border-xs align-middle" role="status" aria-hidden="true"></span></div>
        <div wire:loading.remove>' . $title . '</div>
    ';
    return $loadingSpinner;
}

function loadingStateStatus($key, $title)
{
    $loadingSpinner = '
        <div wire:loading wire:target="' . $key . '" wire:key="' . $key . '"><span class="spinner-border spinner-border-xs" role="status" aria-hidden="true"></span></div> ' . $title . '
    ';
    return $loadingSpinner;
}

function loadingStateWithText($key, $title)
{
    $loadingSpinner = '
        <div wire:loading wire:target="' . $key . '" wire:key="' . $key . '"><span class="spinner-border spinner-border-sm align-middle" role="status" aria-hidden="true"></span> </div> ' . $title . '
    ';

    return $loadingSpinner;
}

function showErrorMessage($message, $file, $line){
    if(env('APP_DEBUG') == 'true'){
        $error_array = [
            'Message' => $message,
            'File' => $file,
            'Line No' => $line
        ];
        return dd($error_array);
    }
}
