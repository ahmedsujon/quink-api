<?php

namespace App\Http\Controllers\api\app\user\auth;

use Exception;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\WaterSetting;
use Illuminate\Http\Request;
use App\Models\UserMeasurement;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthenticationController extends Controller
{
    public function register(Request $request)
    {
        //Validation
        $rules = [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
            'profile_image' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $user = new User();
            $user->name = $request->name;
            $user->username = Str::lower(str_replace(' ', '', $request->name));
            $user->email = $request->email;
            $user->password = Hash::make($request->password);

            if($request->file('profile_image')){
                $avatar = uploadFile($request->file('profile_image'), 'profile-images');
                $user->avatar = $avatar;
            }

            $user->save();

            if ($user) {
                $ttl = 43200;
                $credentials = $request->only('email', 'password');
                if ($token = $this->guard()->attempt($credentials)) {
                    return $this->respondWithToken($token, $ttl);
                }
            } else {
                return response()->json(['status' => 'false']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function login(Request $request)
    {
        //Validation
        $rules = [
            'email' => 'required|email',
            'password' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            // $userStatus = User::find($user->id)->suspended;
            // if ($userStatus == 0) {
            //Login Attempt
            $credentials = $request->only('email', 'password');
            $ttl = 43200;
            if ($request->remember_me == 1) {
                $ttl = 1051200;
            }
            if ($token = $this->guard()->setTTL($ttl)->attempt($credentials)) {
                return $this->respondWithToken($token, $ttl);
            }
            return response()->json(['error' => ['These credentials do not match our records.']], 401);
            // } else {
            //     return response()->json(['result' => 'false', 'message' => 'Your account has been suspended']);
            // }
        } else {
            return response()->json(['error' => ['These credentials do not match our records.']], 401);
        }
    }

    public function userProfile()
    {
        return response()->json($this->guard()->user());
    }

    public function userLogout()
    {
        $this->guard()->logout();

        return response()->json(['result' => 'true', 'message' => 'Successfully logged out']);
    }

    public function refresh()
    {
        return $this->respondWithToken($this->guard()->refresh(), 1440);
    }

    protected function respondWithToken($token, $ttl)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => $ttl,
            'user' => $this->guard()->user(),
        ]);
    }

    public function guard()
    {
        return Auth::guard('user-api');
    }
}