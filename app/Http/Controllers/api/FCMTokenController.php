<?php

namespace App\Http\Controllers\api;

use Exception;
use App\Models\FcmToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FCMTokenController extends Controller
{
    public function storeToken(Request $request)
    {
        $rules = [
            'token' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $getToken = FcmToken::where('token', $request->token)->first();

            if (!$getToken) {
                $token = new FcmToken();
                $token->token = $request->token;
                $token->save();
            }
            return response()->json(['result' => 'true', 'message' => 'Token stored successfully']);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
