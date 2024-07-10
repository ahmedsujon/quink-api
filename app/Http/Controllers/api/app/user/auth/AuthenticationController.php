<?php

namespace App\Http\Controllers\api\app\user\auth;

use Exception;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
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

            if ($request->file('profile_image')) {
                $avatar = uploadFile($request->file('profile_image'), 'profile-images');
                $user->avatar = $avatar;
            }

            $user->save();

            if ($user) {
                $this->send($user->email);
                $ttl = 525600;
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

    public function send($email)
    {
        $otp = rand(1000, 9999);

        $user = User::where('email', $email)->first();
        $user->verification_code = $otp;
        $user->save();

        $data['email'] = $email;
        $data['otp'] = $otp;

        Mail::send('emails.api.email-verification', $data, function ($message) use ($data) {
            $message->to($data['email'])
                ->subject('Email Verification');
        });
    }

    public function verifyEmail(Request $request)
    {
        //Validation
        $rules = [
            'code' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = User::where('id', api_user()->id)->first();
        if ($user->verification_code == $request->code) {
            $user->email_verified_at = now();
            $user->verification_code = null;
            $user->save();

            return response()->json(['status' => true, 'message' => 'Email verified successfully']);
        } else {
            return response()->json(['status' => false, 'message' => 'Incorrect code']);
        }

    }

    public function resendCode(Request $request)
    {
        $this->send(api_user()->email);

        return response()->json(['status' => true, 'message' => 'Verification code sent to your email']);
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
            $ttl = 525600;
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

    public function loginWithGoogle(Request $request)
    {
        // Validation
        $rules = [
            'token' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $googleUser = Socialite::driver('google')->stateless()->userFromToken($request->token);
            $user = User::where('email', $googleUser->getEmail())->first();

            $fullName = $googleUser->getName();
            if ($fullName) {
                $nameParts = explode(' ', $fullName);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
            } else {
                // Fallback if full name is not available
                $firstName = $googleUser->user['given_name'] ?? 'N/A';
                $lastName = $googleUser->user['family_name'] ?? 'N/A';
            }

            $profileImage = $googleUser->getAvatar();
            $client = new Client();
            $response = $client->get($profileImage);
            $imageContents = $response->getBody()->getContents();
            $fileName = uniqid() . Carbon::now()->timestamp. '.jpg';
            $filePath = public_path('uploads/profile-images/' . $fileName);
            if (!File::exists(public_path('uploads/profile-images'))) {
                File::makeDirectory(public_path('uploads/profile-images'), 0755, true);
            }

            if ($user) {
                if (!$user->avatar) {
                    File::put($filePath, $imageContents);
                    $publicUrl = 'uploads/profile-images/' . $fileName;
                    $user->avatar = $publicUrl;
                }
                $user->google_id = $googleUser->getId();
                $user->save();
            } else {
                $user = new User();
                $user->name = $googleUser->getName();
                $user->username = Str::lower(str_replace(' ', '', $googleUser->getName()));
                $user->email = $googleUser->getEmail();
                $user->password = Hash::make($googleUser->getId());
                $user->google_id = $googleUser->getId();
                File::put($filePath, $imageContents);
                $user->avatar = 'uploads/profile-images/' . $fileName;
                $user->save();
            }

            if (!$token = JWTAuth::fromUser($user)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            return $this->respondWithTokenGoogle($token, $user);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Authentication failed', 'message' => $e->getMessage()], 500);
        }
    }

    protected function respondWithTokenGoogle($token, $user)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL(),
            'user' => $user,
        ]);
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
