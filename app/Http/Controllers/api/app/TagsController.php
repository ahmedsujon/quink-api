<?php

namespace App\Http\Controllers\api\app;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostTag;
use Illuminate\Http\Request;

class TagsController extends Controller
{
    public function allTags(Request $request)
    {
        // get my posts with tags
        $taggedPosts = PostTag::join('posts', 'posts.id', 'post_tags.post_id')->where('posts.user_id', api_user()->id)->OrWhereJsonContains('mentioned_users', api_user()->id)->pluck('posts.id')->toArray();

        $paginationValue = $request->per_page ?? 10;

        $posts = Post::select('id', 'title', 'content', 'thumbnail', 'type')->whereIn('id', $taggedPosts)->paginate($paginationValue);
        foreach ($posts as $key => $post) {
            $post->content = url('/') . '/' . $post->content;
            if ($post->thumbnail) {
                $post->thumbnail = url('/') . '/' . $post->thumbnail;
            }
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Data retrieve successfully',
            'data' => $posts,
        ]);

    }
}
