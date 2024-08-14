<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Post;
use App\Models\Bookmark;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
            $post = Post::find($request->post_id);

            $getBookmark = Bookmark::where('user_id', api_user()->id)->where('post_id', $request->post_id)->first();
            if (!$getBookmark) {
                $bookmark = new Bookmark();
                $bookmark->user_id = api_user()->id;
                $bookmark->post_id = $request->post_id;
                $bookmark->post_type = $request->post_type;
                $bookmark->save();

                notification($post->user_id, api_user()->id, 'bookmarked your post.', 'bookmark', $request->post_id, NULL);

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

    public function myBookmarks(Request $request)
    {
        try {
            $pMarks = Bookmark::where('user_id', api_user()->id)->where('post_type', 'photo')->get();
            foreach ($pMarks as $key => $pm) {
                $post = Post::find($pm->post_id);
                $pm->photo = $post->content ? url('/') . '/' . $post->content : '';
            }

            $vMarks = Bookmark::where('user_id', api_user()->id)->where('post_type', 'video')->get();
            foreach ($vMarks as $key => $vm) {
                $post = Post::find($vm->post_id);
                $vm->video = [
                    'video' => $post->content ? url('/') . '/' . $post->content : '',
                    'thumbnail' => $post->thumbnail ? url('/') . '/' . $post->thumbnail : ''
                ];
            }

            $bookmarks = [];

            $bookmarks['photos'] = [
                'total' => $pMarks->count(),
                'data' => $pMarks,
            ];
            $bookmarks['videos'] = [
                'total' => $vMarks->count(),
                'data' => $vMarks,
            ];
            return response()->json([
                'status_code' => 200,
                'message' => 'Data retrieve successfully',
                'data' => $bookmarks,
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
