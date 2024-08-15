<?php

namespace App\Http\Controllers\api\app;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function storePost(Request $request)
    {
        //Validation
        $rules = [
            // 'title' => 'required',
            // 'description' => 'required',
            'content' => 'required',
            'type' => 'required',
            // 'hash_tags' => 'required',
            // 'tags' => 'required',
            // 'link' => 'required',
            // 'music' => 'required',
            'media_type' => 'required',
            'thumbnail' => 'required_if:type,video',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $post = new Post();
            $post->user_id = api_user()->id;
            $post->title = $request->title;
            $post->description = $request->description;
            if ($request->content) {
                $file = uploadFile($request->content, 'post_files');
                $post->content = $file;
            }
            $post->hash_tags = $request->hash_tags;
            $post->tags = $request->tags;
            $post->link = $request->link;
            $post->music = $request->music;
            $post->type = $request->type; // 'photo', 'video', 'story'
            $post->media_type = $request->media_type; // 'photo', 'video'
            if ($request->thumbnail) {
                $thumbnail = uploadFile($request->thumbnail, 'post_thumbs');
                $post->thumbnail = $thumbnail;
            }
            $post->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'New post added successfully',
                'data' => [],
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }

    }

    public function editPost(Request $request)
    {
        try {
            $post = Post::select('id', 'title', 'description', 'content', 'hash_tags', 'tags', 'link', 'music', 'type', 'media_type', 'thumbnail')->find($request->post_id);

            $post->content = url('/') . '/' . $post->content;
            $post->thumbnail = $post->thumbnail ? url('/') . '/' . $post->thumbnail : null;

            return response()->json([
                'status_code' => 200,
                'message' => 'Data retrieve successfully',
                'data' => $post,
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function updatePost(Request $request)
    {
        //Validation
        $rules = [
            'post_id' => 'required',
            // 'title' => 'required',
            // 'description' => 'required',
            'content' => 'required',
            'type' => 'required',
            // 'hash_tags' => 'required',
            // 'tags' => 'required',
            // 'link' => 'required',
            // 'music' => 'required',
            'media_type' => 'required',
            'thumbnail' => 'required_if:type,video',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $post = Post::find($request->post_id);
            $post->title = $request->title;
            $post->description = $request->description;
            if ($request->content) {
                $file = uploadFile($request->content, 'post_files');
                $post->content = $file;
            }
            $post->hash_tags = $request->hash_tags;
            $post->tags = $request->tags;
            $post->link = $request->link;
            $post->music = $request->music;
            $post->type = $request->type; // 'photo', 'video', 'story'
            $post->media_type = $request->media_type; // 'photo', 'video'
            if ($request->thumbnail) {
                $thumbnail = uploadFile($request->thumbnail, 'post_thumbs');
                $post->thumbnail = $thumbnail;
            }
            $post->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Post updated successfully',
                'data' => [],
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }

    }

    public function deletePost(Request $request)
    {
        try {
            $post = Post::find($request->post_id);
            if ($post) {
                $post->delete();
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Post deleted successfully',
                    'data' => [],
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Post not found',
                    'data' => [],
                ]);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function postTagUsers(Request $request)
    {
        try {
            $users = DB::table('users')->select('id', 'name', 'username', 'avatar')->where('name', 'like', '%' . $request->search_value . '%')->get();

            foreach ($users as $key => $user) {
                $user->avatar = url('/') . '/' . $user->avatar;
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Data retrieve successfully',
                'data' => $users,
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

}
