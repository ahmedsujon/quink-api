<?php

namespace App\Http\Controllers\api\app;

use Exception;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use Illuminate\Support\Facades\Validator;

class BookmarkController extends Controller
{
    public function addToBookmark(Request $request)
    {
        //Validation
        $rules = [
            'post_id' => 'required',
            'post_type' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $getBookmark = Bookmark::where('user_id', api_user()->id)->where('post_id', $request->post_id)->first();
            if (!$getBookmark) {
                $bookmark = new Bookmark();
                $bookmark->user_id = api_user()->id;
                $bookmark->post_id = $request->post_id;
                $bookmark->post_type = $request->post_type;
                $bookmark->save();

                return response()->json(['status' => true, 'message' => 'Bookmark Added']);
            } else {
                $getBookmark->delete();
                return response()->json(['status' => true, 'message' => 'Removed From Bookmark']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function bookmarkStatus(Request $request)
    {
        //Validation
        $rules = [
            'post_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $getBookmark = Bookmark::where('user_id', api_user()->id)->where('post_id', $request->post_id)->first();
            if (!$getBookmark) {
                return response()->json(['status' => false, 'message' => 'Not Bookmarked']);
            } else {
                return response()->json(['status' => true, 'message' => 'Bookmarked']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}