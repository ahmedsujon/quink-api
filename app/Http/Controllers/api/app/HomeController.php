<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Like;

class HomeController extends Controller
{
    public function trendingPosts(Request $request)
    {
        try {
            $posts = Post::select('id', 'caption', 'content', 'type', 'tags', 'views')->orderBy('id', 'DESC')->get();

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();

                $post->comments = Comment::where('post_id', $post->id)->get();
            }

            if($posts->count() > 0){
                return response()->json($posts);
            } else {
                return response()->json(['result' => 'false', 'message' => 'No Posts Found']);
            }

        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
