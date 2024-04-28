<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Follower;
use App\Models\Like;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    // trending -> Posts From Random Users
    public function trendingPhotos(Request $request)
    {
        try {
            $search_term = $request->search_value;
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views', 'user_id as owner_info')->where('title', 'like', '%'. $search_term .'%')->where('type', 'photo')->orderBy('id', 'DESC')->paginate($pagination_value);

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

                $post->owner_info = post_owner_info($post->owner_info);
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
            $search_term = $request->search_value;
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views', 'user_id as owner_info')->where('title', 'like', '%'. $search_term .'%')->where('type', 'video')->orderBy('id', 'DESC')->paginate($pagination_value);

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

                $post->owner_info = post_owner_info($post->owner_info);
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
            $search_term = $request->search_value;
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views', 'user_id as owner_info')->where('title', 'like', '%'. $search_term .'%')->where('type', 'story')->orderBy('id', 'DESC')->paginate($pagination_value);

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

                $post->owner_info = post_owner_info($post->owner_info);
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

    // following -> Posts From Following Users
    public function followingPhotos(Request $request)
    {
        try {
            $search_term = $request->search_value;
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $myFollowing = Follower::where('follower_id', api_user()->id)->pluck('user_id')->toArray();

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views', 'user_id as owner_info')->where('title', 'like', '%'. $search_term .'%')->where('type', 'photo')->orderBy('id', 'DESC')->whereIn('user_id', $myFollowing)->paginate($pagination_value);

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

                $post->owner_info = post_owner_info($post->owner_info);
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
    public function followingVideos(Request $request)
    {
        try {
            $search_term = $request->search_value;
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $myFollowing = Follower::where('follower_id', api_user()->id)->pluck('user_id')->toArray();

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views', 'user_id as owner_info')->where('title', 'like', '%'. $search_term .'%')->where('type', 'video')->orderBy('id', 'DESC')->whereIn('user_id', $myFollowing)->paginate($pagination_value);

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

                $post->owner_info = post_owner_info($post->owner_info);
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
    public function followingStories(Request $request)
    {
        try {
            $search_term = $request->search_value;
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $myFollowing = Follower::where('follower_id', api_user()->id)->pluck('user_id')->toArray();

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'views', 'user_id as owner_info')->where('title', 'like', '%'. $search_term .'%')->where('type', 'story')->orderBy('id', 'DESC')->whereIn('user_id', $myFollowing)->paginate($pagination_value);

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

                $post->owner_info = post_owner_info($post->owner_info);
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
