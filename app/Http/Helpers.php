<?php

use Carbon\Carbon;
use App\Models\Food;
use App\Models\Admin;
use App\Models\Setting;
use App\Models\Permission;
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
