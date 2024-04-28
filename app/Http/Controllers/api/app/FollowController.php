<?php

namespace App\Http\Controllers\api\app;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Follower;
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
            $getFollow = Follower::where('user_id', api_user()->id)->where('follower_id', $request->user_id)->first();
            if (!$getFollow) {
                $follow = new Follower();
                $follow->user_id = api_user()->id;
                $follow->follower_id = $request->user_id;
                $follow->save();

                return response()->json(['status' => true, 'message' => 'Follow Success']);
            } else {
                $getFollow->delete();

                return response()->json(['status' => true, 'message' => 'Un-Follow Success']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function followStatus(Request $request)
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
            $getFollow = Follower::where('user_id', api_user()->id)->where('follower_id', $request->user_id)->first();
            if (!$getFollow) {
                return response()->json(['status' => false, 'message' => 'Not Following']);
            } else {
                return response()->json(['status' => true, 'message' => 'Following']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
