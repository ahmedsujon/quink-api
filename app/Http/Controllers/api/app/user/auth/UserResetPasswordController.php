<?php

namespace App\Http\Controllers\api\app\user\auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserResetPasswordController extends Controller
{
    public function sendEmail(Request $request)
    {
        $rules = [
            'email' => 'required|email',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $getUser = User::where('email', $request->email)->first();
        if ($getUser != '') {
            $this->send($request->email);
            return $this->successResponse();
        } else {
            return $this->failedResponse();
        }
    }

    public function send($email)
    {
        $data['email'] = $email;
        $data['token'] = $this->createToken($email);

        Mail::send('emails.api.forget-password', $data, function ($message) use ($data) {
            $message->to($data['email'])
                ->subject('Reset Password');
        });
    }

    public function createToken($email)
    {
        $oldToken = DB::table('password_reset_tokens')->where('email', $email)->first();

        if ($oldToken) {
            $otp = rand(1000, 9999);
            DB::table('password_reset_tokens')->update([
                'otp' => $otp,
                'created_at' => Carbon::now(),
            ]);
            return [
                'token' => $oldToken->token,
                'otp' => $otp,
            ];
        } else {
            $token = Str::random(40);
            $otp = rand(1000, 9999);

            DB::table('password_reset_tokens')->insert([
                'email' => $email,
                'token' => $token,
                'otp' => $otp,
                'created_at' => Carbon::now(),
            ]);

            return [
                'token' => $token,
                'otp' => $otp,
            ];
        }
    }

    public function failedResponse()
    {
        return response()->json([
            'error' => 'No user found with this email',
        ], Response::HTTP_NOT_FOUND);
    }

    public function successResponse()
    {
        return response()->json([
            'success' => 'Password reset email has beed sent successfully, please check your inbox.',
        ], Response::HTTP_OK);
    }

    //validate otp
    public function validateOtp(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'otp' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $getRequest = DB::table('password_reset_tokens')->where('email', $request->email)->where('otp', $request->otp)->first();
        if ($getRequest) {
            DB::table('password_reset_tokens')->where('email', $request->email)->where('otp', $request->otp)->delete();
            return response()->json(['result' => true, 'message' => 'Good to go!']);
        } else {
            return response()->json([
                'result' => false,
                'message' => 'Incorrect OTP',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }

    //Change Password
    public function changePassword(Request $request)
    {
        $rules = [
            'email' => 'required|email',
            'password' => 'required|min:8',
            'confirm_password' => 'required|min:8|same:password',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $getUser = DB::table('users')->where('email', $request->email)->first();
        if ($getUser) {
            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            return response()->json(['result' => true, 'message' => 'Password updated successfully']);
        } else {
            return response()->json([
                'result' => false,
                'message' => 'No user found!',
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }
    }
}
