<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Like;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class LikeController extends Controller
{
    public function LikeUnlike(Request $request)
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
            $post = Post::find($request->post_id);

            $getLike = Like::where('user_id', api_user()->id)->where('post_id', $request->post_id)->first();
            if (!$getLike) {
                $like = new Like();
                $like->user_id = api_user()->id;
                $like->post_id = $request->post_id;
                $like->save();

                notification($post->user_id, api_user()->id, 'Likes your post.', 'like', $request->post_id, NULL);

                return response()->json(['status' => true, 'message' => 'Like Success']);
            } else {
                $getLike->delete();

                return response()->json(['status' => true, 'message' => 'Un-Like Success']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function likeStatus(Request $request)
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
            $getLike = Like::where('user_id', api_user()->id)->where('post_id', $request->post_id)->first();
            if (!$getLike) {
                return response()->json(['status' => false, 'message' => 'Not Liked']);
            } else {
                return response()->json(['status' => true, 'message' => 'Liked']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
