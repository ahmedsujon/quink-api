<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function trendingPhotos(Request $request)
    {
        try {
            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views')->where('type', 'photo')->orderBy('id', 'DESC')->get();

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                foreach ($post->tags as $tag_id) {
                    $user = DB::table('users')->select('name', 'avatar')->find($tag_id);
                    $user->avatar = url('/') . '/' . $user->avatar;

                    $tags[] = [
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                    ];
                }
                $post->tags = $tags;


                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();

                $comments = Comment::select('id', 'post_id', 'comment', 'created_at')->where('post_id', $post->id)->where('parent_id', NULL)->get();

                foreach ($comments as $comment) {
                    $replies = Comment::select('id', 'post_id', 'comment', 'created_at')->where('post_id', $post->id)->where('parent_id', $comment->id)->get();

                    foreach($replies as $reply) {
                        $reply->likes = CommentLike::where('comment_id', $reply->id)->count();
                        $reply->created_time = short_time($reply->created_at);
                    }

                    $comment->likes = CommentLike::where('comment_id', $comment->id)->count();
                    $comment->created_time = short_time($comment->created_at);
                    $comment->total_replies = $replies->count();
                    $comment->replies = $replies;
                }

                $post->comments = $comments;
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

    public function trendingVideos(Request $request)
    {
        try {
            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views')->where('type', 'video')->orderBy('id', 'DESC')->get();

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                foreach ($post->tags as $tag_id) {
                    $user = DB::table('users')->select('name', 'avatar')->find($tag_id);
                    $user->avatar = url('/') . '/' . $user->avatar;

                    $tags[] = [
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                    ];
                }
                $post->tags = $tags;


                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();

                $comments = Comment::select('id', 'post_id', 'comment', 'created_at')->where('post_id', $post->id)->where('parent_id', NULL)->get();

                foreach ($comments as $comment) {
                    $replies = Comment::select('id', 'post_id', 'comment', 'created_at')->where('post_id', $post->id)->where('parent_id', $comment->id)->get();

                    foreach($replies as $reply) {
                        $reply->likes = CommentLike::where('comment_id', $reply->id)->count();
                        $reply->created_time = short_time($reply->created_at);
                    }

                    $comment->likes = CommentLike::where('comment_id', $comment->id)->count();
                    $comment->created_time = short_time($comment->created_at);
                    $comment->total_replies = $replies->count();
                    $comment->replies = $replies;
                }

                $post->comments = $comments;
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

    public function trendingStories(Request $request)
    {
        try {
            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views')->where('type', 'story')->orderBy('id', 'DESC')->get();

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                foreach ($post->tags as $tag_id) {
                    $user = DB::table('users')->select('name', 'avatar')->find($tag_id);
                    $user->avatar = url('/') . '/' . $user->avatar;

                    $tags[] = [
                        'name' => $user->name,
                        'avatar' => $user->avatar,
                    ];
                }
                $post->tags = $tags;


                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();

                $comments = Comment::select('id', 'post_id', 'comment', 'created_at')->where('post_id', $post->id)->where('parent_id', NULL)->get();

                foreach ($comments as $comment) {
                    $replies = Comment::select('id', 'post_id', 'comment', 'created_at')->where('post_id', $post->id)->where('parent_id', $comment->id)->get();

                    foreach($replies as $reply) {
                        $reply->likes = CommentLike::where('comment_id', $reply->id)->count();
                        $reply->created_time = short_time($reply->created_at);
                    }

                    $comment->likes = CommentLike::where('comment_id', $comment->id)->count();
                    $comment->created_time = short_time($comment->created_at);
                    $comment->total_replies = $replies->count();
                    $comment->replies = $replies;
                }

                $post->comments = $comments;
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
