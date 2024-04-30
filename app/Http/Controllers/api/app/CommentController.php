<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function addComment(Request $request)
    {
        //Validation
        $rules = [
            'post_id' => 'required',
            'comment' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $getPost = Post::find($request->post_id);
            if ($getPost) {
                $comment = new Comment();
                $comment->user_id = api_user()->id;
                $comment->parent_id = $request->comment_id ? $request->comment_id : NULL;
                $comment->post_id = $request->post_id;
                $comment->comment = $request->comment;
                $comment->status = 1;
                $comment->save();

                return response()->json(['status' => true, 'message' => 'Comment added successfully']);
            } else {
                return response()->json(['status' => false, 'message' => 'Something went wrong!']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function LikeUnlike(Request $request)
    {
        //Validation
        $rules = [
            'comment_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $getLike = CommentLike::where('user_id', api_user()->id)->where('comment_id', $request->comment_id)->first();
            if (!$getLike) {
                $like = new CommentLike();
                $like->user_id = api_user()->id;
                $like->comment_id = $request->comment_id;
                $like->save();

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
            'comment_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $getLike = CommentLike::where('user_id', api_user()->id)->where('comment_id', $request->comment_id)->first();
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
