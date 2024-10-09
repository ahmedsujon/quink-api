<?php

use Carbon\Carbon;
use App\Models\User;
use App\Models\Admin;
use GuzzleHttp\Client;
use App\Models\FcmToken;
use App\Models\Follower;
use App\Models\Permission;
use App\Models\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Google\Auth\Credentials\ServiceAccountCredentials;

function admin()
{
    return Auth::guard('admin')->user();
}

function getAdminByID($id)
{
    return Admin::find($id);
}

// Api
function api_user()
{
    return Auth::guard('user-api')->user();
}

function getUserByID($user_id)
{
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
    $user->avatar = url('/') . '/' . $user->avatar;
    $user->is_verified = $user->is_verified ? 1 : 0;

    $follow = Follower::where('user_id', $user_id)->where('follower_id', $auth_id)->first();
    $user->is_following = $follow ? 1 : 0;

    return $user;
}
function post_owner_info_stories($user_id)
{
    $user = DB::table('users')->select('id', 'name', 'avatar', 'email_verified_at as is_verified')->find($user_id);
    $user->avatar = url('/') . '/' . $user->avatar;
    $user->is_verified = $user->is_verified ? 1 : 0;

    return $user;
}

function comment_user_info($user_id)
{
    $user = DB::table('users')->select('id', 'name', 'avatar', 'email_verified_at as is_verified')->find($user_id);
    $user->avatar = url('/') . '/' . $user->avatar;
    $user->is_verified = $user->is_verified ? 1 : 0;
    return $user;
}

function notification($for, $user_id, $notification_text, $type, $post_id = null, $comment_id = null)
{
    if ($type == 'follow') {
        Notification::where('type', 'follow')->where('user_id', $user_id)->where('notification_for', $for)->delete();

        $title = 'Follow';
    }
    if ($type == 'like') {
        Notification::where('type', 'like')->where('user_id', $user_id)->where('notification_for', $for)->delete();

        $title = 'Like';
    }
    if ($type == 'comment') {
        Notification::where('type', 'comment')->where('user_id', $user_id)->where('notification_for', $for)->delete();

        $title = 'Comment';
    }
    if ($type == 'comment_reply') {
        Notification::where('type', 'comment_reply')->where('user_id', $user_id)->where('notification_for', $for)->delete();

        $title = 'Comment Reply';
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

        $post = DB::table('posts')->find($post_id);
        $post_type = $post ? $post->type : 'photo';

        pushNotification($for, $title, $notification_text, $post_id, $post_type, $comment_id);
    }

}

function uploadFile($file, $folder)
{
    $fileName = uniqid() . Carbon::now()->timestamp . '.' . $file->extension();
    $file->storeAs($folder, $fileName);

    $file_name = 'uploads/' . $folder . '/' . $fileName;
    return $file_name;
}

/**
 * @param $n
 * @return string
 * Use to convert large positive numbers in to short form like 1K+, 100K+, 199K+, 1M+, 10M+, 1B+ etc
 */
function number_format_short($n)
{
    $n_format = 0;
    $suffix = '';

    if ($n > 0 && $n < 1000) {
        // 1 - 999
        $n_format = floor($n);
        $suffix = '';
    } else if ($n >= 1000 && $n < 1000000) {
        // 1k-999k
        $n_format = floor($n / 1000);
        $suffix = 'K';
    } else if ($n >= 1000000 && $n < 1000000000) {
        // 1m-999m
        $n_format = floor($n / 1000000);
        $suffix = 'M';
    } else if ($n >= 1000000000 && $n < 1000000000000) {
        // 1b-999b
        $n_format = floor($n / 1000000000);
        $suffix = 'B';
    } else if ($n >= 1000000000000) {
        // 1t+
        $n_format = floor($n / 1000000000000);
        $suffix = 'T';
    }

    return !empty($n_format . $suffix) ? $n_format . $suffix : 0;
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

function showErrorMessage($message, $file, $line)
{
    if (env('APP_DEBUG') == 'true') {
        $error_array = [
            'Message' => $message,
            'File' => $file,
            'Line No' => $line,
        ];
        return dd($error_array);
    }
}


function pushNotification($user_id, $title, $body, $post_id, $post_type, $comment_id)
{
    $FcmToken = FcmToken::where('user_id', $user_id)->where('status', 1)->pluck('token')->first();

    // Check if there are tokens to send notification to
    if (empty($FcmToken)) {
        Log::warning('No active FCM tokens found for user ID: ' . $user_id);
        return false;
    }

    // Path to your service account file (downloaded from Firebase console)
    $serviceAccountFile = 'firebase/quink-app-24-firebase-adminsdk-1f0uw-861faa7b77.json';

    // Authenticate and get OAuth 2.0 token
    $accessToken = getAccessToken($serviceAccountFile);

    $notification_body = [
        'notification_text' => $body,
        'post_id' => $post_id,
        'post_type' => $post_type,
        'comment_id' => $comment_id
    ];
    // Prepare notification payload
    $data = [
        "message" => [
            "token" => $FcmToken, // Single FCM token
            "notification" => [
                "title" => $title,
                "body" => json_encode($notification_body),
            ]
        ]
    ];

    // Send the notification using the HTTP v1 API
    $url = 'https://fcm.googleapis.com/v1/projects/'. env('FIREBASE_PROJECT_ID') .'/messages:send';
    $client = new Client();
    $response = $client->post($url, [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken,
            'Content-Type' => 'application/json',
        ],
        'json' => $data,
    ]);

    if ($response->getStatusCode() !== 200) {
        Log::error('Push notification failed: ' . $response->getBody());
        return false;
    }

    Log::info('Push notification sent successfully: ' . $response->getBody());
    return true;
}

// Helper function to get OAuth 2.0 Access Token
function getAccessToken($serviceAccountFile)
{
    // Define the required scope for Firebase Messaging
    $scopes = ['https://www.googleapis.com/auth/firebase.messaging'];

    // Load the service account credentials
    $credentials = new ServiceAccountCredentials($scopes, $serviceAccountFile);

    // Generate the access token
    $authToken = $credentials->fetchAuthToken();

    // Check for errors
    if (!isset($authToken['access_token'])) {
        throw new Exception('Failed to obtain access token from Firebase.');
    }

    return $authToken['access_token'];
}
