<?php

namespace App\Http\Controllers\api\app;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class FollowController extends Controller
{
    public function followUnFollow(Request $request)
    {
        //Validation
        $rules = [
            'user_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {

        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
