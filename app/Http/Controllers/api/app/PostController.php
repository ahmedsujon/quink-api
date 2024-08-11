<?php

namespace App\Http\Controllers\api\app;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function storePost(Request $request)
    {
        //Validation
        $rules = [
            'title' => 'required',
            'description' => 'required',
            'content' => 'required',
            'type' => 'required',
            'hash_tags' => 'required',
            'tags' => 'required',
            'link' => 'required',
            'music' => 'required',
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

}
