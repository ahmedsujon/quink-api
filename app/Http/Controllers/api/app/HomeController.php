<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Like;
use App\Models\Post;
use App\Models\Comment;
use App\Models\Bookmark;
use App\Models\Follower;
use App\Models\CommentLike;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    // trending -> Posts From Random Users
    public function trendingPhotos(Request $request)
    {
        try {
            $search_term = $request->search_value;
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'link', 'music', 'views', 'user_id as owner_info', 'created_at')
                ->when($search_term, function ($query) use ($search_term) {
                    return $query->where('title', 'like', '%' . $search_term . '%');
                })
                ->where('type', 'photo')->orderBy('id', 'DESC')
                ->paginate($pagination_value);

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                if ($post->tags) {
                    foreach ($post->tags as $tag_id) {
                        $user = DB::table('users')->select('id', 'name', 'avatar')->find($tag_id);
                        $user->avatar = url('/') . '/' . $user->avatar;

                        $tags[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'avatar' => $user->avatar,
                        ];
                    }
                }
                $post->tags = $tags;
                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();
                $post->owner_info = post_owner_info($post->owner_info, $request->authenticated_user_id);
                if ($request->authenticated_user_id) {
                    $like = Like::where('user_id', $request->authenticated_user_id)->where('post_id', $post->id)->first();
                    $bookmark = Bookmark::where('user_id', $request->authenticated_user_id)->where('post_id', $post->id)->first();
                    $post->is_reacted = $like ? 1 : 0;
                    $post->is_bookmarked = $bookmark ? 1 : 0;
                } else {
                    $post->is_reacted = 0;
                    $post->is_bookmarked = 0;
                }

            }

            if ($posts->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $posts,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No data available',
                    'data' => [],
                ]);
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

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'link', 'music', 'views', 'user_id as owner_info', 'created_at')
                ->when($search_term, function ($query) use ($search_term) {
                    return $query->where('title', 'like', '%' . $search_term . '%');
                })
                ->where('type', 'video')->orderBy('id', 'DESC')
                ->paginate($pagination_value);

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                if ($post->tags) {
                    foreach ($post->tags as $tag_id) {
                        $user = DB::table('users')->select('id', 'name', 'avatar')->find($tag_id);
                        $user->avatar = url('/') . '/' . $user->avatar;

                        $tags[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'avatar' => $user->avatar,
                        ];
                    }
                }

                $post->tags = $tags;
                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();
                $post->owner_info = post_owner_info($post->owner_info, $request->authenticated_user_id);
                if ($request->authenticated_user_id) {
                    $like = Like::where('user_id', $request->authenticated_user_id)->where('post_id', $post->id)->first();
                    $bookmark = Bookmark::where('user_id', $request->authenticated_user_id)->where('post_id', $post->id)->first();
                    $post->is_reacted = $like ? 1 : 0;
                    $post->is_bookmarked = $bookmark ? 1 : 0;
                } else {
                    $post->is_reacted = 0;
                    $post->is_bookmarked = 0;
                }

            }

            if ($posts->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $posts,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No data available',
                    'data' => [],
                ]);
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

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'link', 'music', 'views', 'user_id as owner_info', 'created_at')
                ->when($search_term, function ($query) use ($search_term) {
                    return $query->where('title', 'like', '%' . $search_term . '%');
                })
                ->where('type', 'story')->orderBy('id', 'DESC')
                ->paginate($pagination_value);

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                if ($post->tags) {
                    foreach ($post->tags as $tag_id) {
                        $user = DB::table('users')->select('id', 'name', 'avatar')->find($tag_id);
                        $user->avatar = url('/') . '/' . $user->avatar;

                        $tags[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'avatar' => $user->avatar,
                        ];
                    }
                }
                $post->tags = $tags;
                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();
                $post->owner_info = post_owner_info($post->owner_info, $request->authenticated_user_id);
                if ($request->authenticated_user_id) {
                    $like = Like::where('user_id', $request->authenticated_user_id)->where('post_id', $post->id)->first();
                    $bookmark = Bookmark::where('user_id', $request->authenticated_user_id)->where('post_id', $post->id)->first();
                    $post->is_reacted = $like ? 1 : 0;
                    $post->is_bookmarked = $bookmark ? 1 : 0;
                } else {
                    $post->is_reacted = 0;
                    $post->is_bookmarked = 0;
                }

            }

            if ($posts->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $posts,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No data available',
                    'data' => [],
                ]);
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

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'link', 'music', 'views', 'user_id as owner_info')
                ->when($search_term, function ($query) use ($search_term) {
                    return $query->where('title', 'like', '%' . $search_term . '%');
                })
                ->where('type', 'photo')->orderBy('id', 'DESC')
                ->whereIn('user_id', $myFollowing)->paginate($pagination_value);

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                if ($post->tags) {
                    foreach ($post->tags as $tag_id) {
                        $user = DB::table('users')->select('id', 'name', 'avatar')->find($tag_id);
                        $user->avatar = url('/') . '/' . $user->avatar;

                        $tags[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'avatar' => $user->avatar,
                        ];
                    }
                }
                $post->tags = $tags;
                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();
                $post->owner_info = post_owner_info($post->owner_info, api_user()->id);
                if (api_user()) {
                    $like = Like::where('user_id', api_user()->id)->where('post_id', $post->id)->first();
                    $bookmark = Bookmark::where('user_id', api_user()->id)->where('post_id', $post->id)->first();
                    $post->is_reacted = $like ? 1 : 0;
                    $post->is_bookmarked = $bookmark ? 1 : 0;
                } else {
                    $post->is_reacted = 0;
                    $post->is_bookmarked = 0;
                }

            }

            if ($posts->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $posts,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No data available',
                    'data' => [],
                ]);
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

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'link', 'music', 'views', 'user_id as owner_info')
                ->when($search_term, function ($query) use ($search_term) {
                    return $query->where('title', 'like', '%' . $search_term . '%');
                })
                ->where('type', 'video')->orderBy('id', 'DESC')
                ->whereIn('user_id', $myFollowing)->paginate($pagination_value);

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                if ($post->tags) {
                    foreach ($post->tags as $tag_id) {
                        $user = DB::table('users')->select('id', 'name', 'avatar')->find($tag_id);
                        $user->avatar = url('/') . '/' . $user->avatar;

                        $tags[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'avatar' => $user->avatar,
                        ];
                    }
                }
                $post->tags = $tags;
                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();
                $post->owner_info = post_owner_info($post->owner_info, api_user()->id);
                if (api_user()) {
                    $like = Like::where('user_id', api_user()->id)->where('post_id', $post->id)->first();
                    $bookmark = Bookmark::where('user_id', api_user()->id)->where('post_id', $post->id)->first();
                    $post->is_reacted = $like ? 1 : 0;
                    $post->is_bookmarked = $bookmark ? 1 : 0;
                } else {
                    $post->is_reacted = 0;
                    $post->is_bookmarked = 0;
                }

            }

            if ($posts->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $posts,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No data available',
                    'data' => [],
                ]);
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

            $posts = Post::select('id', 'title', 'description', 'content', 'type', 'hash_tags', 'tags', 'link', 'music', 'views', 'user_id as owner_info')
                ->when($search_term, function ($query) use ($search_term) {
                    return $query->where('title', 'like', '%' . $search_term . '%');
                })
                ->where('type', 'story')->orderBy('id', 'DESC')
                ->whereIn('user_id', $myFollowing)->paginate($pagination_value);

            foreach ($posts as $key => $post) {
                if ($post->type == 'photo' || $post->type == 'video' || $post->type == 'story') {
                    $post->content = url('/') . '/' . $post->content;
                } else {
                    $post->content = $post->content;
                }

                $tags = [];
                if ($post->tags) {
                    foreach ($post->tags as $tag_id) {
                        $user = DB::table('users')->select('id', 'name', 'avatar')->find($tag_id);
                        $user->avatar = url('/') . '/' . $user->avatar;

                        $tags[] = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'avatar' => $user->avatar,
                        ];
                    }
                }
                $post->tags = $tags;
                $post->total_like = Like::where('post_id', $post->id)->count();
                $post->total_comment = Comment::where('post_id', $post->id)->count();
                $post->owner_info = post_owner_info($post->owner_info, api_user()->id);
                if (api_user()) {
                    $like = Like::where('user_id', api_user()->id)->where('post_id', $post->id)->first();
                    $bookmark = Bookmark::where('user_id', api_user()->id)->where('post_id', $post->id)->first();
                    $post->is_reacted = $like ? 1 : 0;
                    $post->is_bookmarked = $bookmark ? 1 : 0;
                } else {
                    $post->is_reacted = 0;
                    $post->is_bookmarked = 0;
                }

            }

            if ($posts->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $posts,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No data available',
                    'data' => [],
                ]);
            }

        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function comments(Request $request)
    {
        try {
            $pagination_value = $request->per_page ? $request->per_page : 10;
            $comments = Comment::select('id', 'post_id', 'comment', 'user_id as user_info', 'created_at')->where('post_id', $request->post_id)->where('parent_id', null)->orderBy('id', 'DESC')->paginate($pagination_value);

            foreach ($comments as $comment) {
                $replies = Comment::select('id', 'post_id', 'comment', 'user_id as user_info', 'created_at')->where('post_id', $request->post_id)->where('parent_id', $comment->id)->orderBy('id', 'DESC')->get();

                foreach ($replies as $reply) {
                    $reply->likes = CommentLike::where('comment_id', $reply->id)->count();
                    if ($request->authenticated_user_id) {
                        $like = CommentLike::where('user_id', $request->authenticated_user_id)->where('comment_id', $reply->id)->first();
                        $reply->is_liked = $like ? 1 : 0;
                    } else {
                        $reply->is_liked = 0;
                    }
                    $reply->user_info = comment_user_info($reply->user_info);
                    $reply->created_time = short_time($reply->created_at);
                }

                $comment->likes = CommentLike::where('comment_id', $comment->id)->count();
                if ($request->authenticated_user_id) {
                    $like = CommentLike::where('user_id', $request->authenticated_user_id)->where('comment_id', $comment->id)->first();
                    $comment->is_liked = $like ? 1 : 0;
                } else {
                    $comment->is_liked = 0;
                }
                $comment->created_time = short_time($comment->created_at);
                $comment->total_replies = $replies->count();
                $comment->replies = $replies;
                $comment->user_info = comment_user_info($comment->user_info);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Data retrieve successfully',
                'data' => [
                    'total_comments' => Comment::where('post_id', $request->post_id)->count(),
                    'comments' => $comments,
                ],
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }

    }
    public function stories(Request $request)
    {
        try {
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $myFollowing = Follower::where('follower_id', api_user()->id)->pluck('user_id')->toArray();

            $posts = Post::select('id as post_id', 'content', 'type', 'user_id as user_info')->where('type', 'story')->orderBy('id', 'DESC')->whereIn('user_id', $myFollowing)->paginate($pagination_value);

            foreach ($posts as $key => $post) {
                $post->content = url('/') . '/' . $post->content;
                $post->user_info = post_owner_info_stories($post->user_info);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Data retrieve successfully',
                'data' => $posts,
            ]);

        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function userStories(Request $request)
    {
        try {
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $posts = Post::select('id as post_id', 'content', 'type', 'user_id as user_info')->where('type', 'story')->orderBy('id', 'DESC')->where('user_id', $request->user_id)->paginate($pagination_value);

            foreach ($posts as $key => $post) {
                $post->content = url('/') . '/' . $post->content;
                $post->user_info = post_owner_info_stories($post->user_info);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Data retrieve successfully',
                'data' => $posts,
            ]);

        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function postViewCount(Request $request)
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
            if ($post) {
                $post->views += 1;
                $post->save();

                return response()->json([
                    'status_code' => 200,
                    'message' => 'Post view added',
                    'data' => []
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'Post not found',
                    'data' => []
                ]);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}

