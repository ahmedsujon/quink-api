<?php

namespace App\Http\Controllers\api\app;

use App\Http\Controllers\Controller;
use App\Models\Follower;
use App\Models\Like;
use App\Models\Post;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function myProfile(Request $request)
    {
        try {
            $user = User::where('id', api_user()->id)->first();
            $data = [];
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'No data found']);
            } else {
                $data['name'] = $user->name;
                $data['user_name'] = $user->username;
                $data['member_since'] = Carbon::parse($user->created_at)->format('M d, Y');
                $data['profile_image'] = $user->avatar ? url('/') . '/' . $user->avatar : 'assets/images/placeholder.jpg';
                $data['websites'] = $user->websites;
                $data['location'] = $user->location;
                $data['bio'] = $user->bio;
                $data['followers'] = Follower::where('user_id', api_user()->id)->count();
                $data['following'] = Follower::where('follower_id', api_user()->id)->count();
                $data['posts'] = Post::where('user_id', api_user()->id)->count();
                $data['likes'] = Like::join('posts', 'likes.post_id', 'posts.id')->where('posts.user_id', api_user()->id)->count();
                $data['total_photos'] = Post::select('id')->where('user_id', api_user()->id)->where('type', 'photo')->count();
                $data['total_videos'] = Post::select('id')->where('user_id', api_user()->id)->where('type', 'video')->count();

                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $data,
                ]);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function myPhotos(Request $request)
    {
        $paginationValue = $request->per_page ?? 10;

        $photos = Post::select('id', 'title', 'content')->where('user_id', api_user()->id)->where('type', 'photo')->orderBy('id', 'DESC')->paginate($paginationValue);
        foreach ($photos as $key => $pt) {
            $pt->content = url('/') . '/' . $pt->content;
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Data retrieve successfully',
            'data' => $photos,
        ]);

    }

    public function myVideos(Request $request)
    {
        $paginationValue = $request->per_page ?? 10;

        $videos = Post::select('id', 'title', 'content', 'thumbnail')->where('user_id', api_user()->id)->where('type', 'video')->orderBy('id', 'DESC')->paginate($paginationValue);
        foreach ($videos as $key => $pt) {
            $pt->content = url('/') . '/' . $pt->content;
            $pt->thumbnail = url('/') . '/' . $pt->thumbnail;
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Data retrieve successfully',
            'data' => $videos,
        ]);

    }

    public function myStories(Request $request)
    {
        $paginationValue = $request->per_page ?? 10;

        $stories = Post::select('id', 'title', 'content')->where('user_id', api_user()->id)->where('type', 'story')->orderBy('id', 'DESC')->paginate($paginationValue);
        foreach ($stories as $key => $pt) {
            $pt->content = url('/') . '/' . $pt->content;
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Data retrieve successfully',
            'data' => $stories,
        ]);

    }

    public function updateMyProfile(Request $request)
    {
        //Validation
        $rules = [
            'name' => 'required',
            'user_name' => 'required',
            'websites' => 'required',
            // 'location' => 'required',
            // 'bio' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $user = User::where('id', api_user()->id)->first();
            $user->name = $request->name;
            $user->username = $request->user_name;
            $user->websites = $request->websites;
            $user->location = $request->location;
            $user->bio = $request->bio;

            if ($request->profile_image) {
                $image = uploadFile($request->profile_image, 'profile_images');
                $user->avatar = $image;
            }

            $user->save();

            return response()->json([
                'status_code' => 200,
                'message' => 'Profile updated successfully',
                'data' => [],
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function userProfile(Request $request)
    {
        //Validation
        $rules = [
            'user_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $user = User::where('id', $request->user_id)->first();
            $data = [];
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'No data found']);
            } else {
                $data['name'] = $user->name;
                $data['user_name'] = $user->username;
                $data['member_since'] = Carbon::parse($user->created_at)->format('M d, Y');
                $data['profile_image'] = $user->avatar ? url('/') . '/' . $user->avatar : 'assets/images/placeholder.jpg';
                $data['websites'] = $user->websites;
                $data['location'] = $user->location;
                $data['bio'] = $user->bio;
                $data['followers'] = Follower::where('user_id', $request->user_id)->count();
                $data['following'] = Follower::where('follower_id', $request->user_id)->count();
                $data['posts'] = Post::where('user_id', $request->user_id)->count();
                $data['likes'] = Like::join('posts', 'likes.post_id', 'posts.id')->where('posts.user_id', $request->user_id)->count();
                $data['total_photos'] = Post::select('id')->where('user_id', $request->user_id)->where('type', 'photo')->count();
                $data['total_videos'] = Post::select('id')->where('user_id', $request->user_id)->where('type', 'video')->count();

                if (api_user()) {
                    $follow = Follower::where('user_id', $request->user_id)->where('follower_id', api_user()->id)->first();
                    $data['is_following'] = $follow ? 1 : 0;
                } else {
                    $data['is_following'] = 0;
                }

                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $data,
                ]);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function userPhotos(Request $request)
    {
        $paginationValue = $request->per_page ?? 10;

        $photos = Post::select('id', 'title', 'content')->where('user_id', $request->user_id)->where('type', 'photo')->orderBy('id', 'DESC')->paginate($paginationValue);
        foreach ($photos as $key => $pt) {
            $pt->content = url('/') . '/' . $pt->content;
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Data retrieve successfully',
            'data' => $photos,
        ]);

    }

    public function userVideos(Request $request)
    {
        $paginationValue = $request->per_page ?? 10;

        $videos = Post::select('id', 'title', 'content', 'thumbnail')->where('user_id', $request->user_id)->where('type', 'video')->orderBy('id', 'DESC')->paginate($paginationValue);
        foreach ($videos as $key => $pt) {
            $pt->content = url('/') . '/' . $pt->content;
            $pt->thumbnail = url('/') . '/' . $pt->thumbnail;
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Data retrieve successfully',
            'data' => $videos,
        ]);

    }

    public function userFollowersFollowings(Request $request)
    {
        $paginationValue = $request->per_page ?? 10;

        $type = $request->type;
        if ($type == 'followings') {
            $followings = Follower::where('follower_id', $request->user_id)->pluck('user_id')->toArray();
            $all_data = User::select('id', 'name', 'avatar as image')->whereIn('id', $followings)->paginate($paginationValue);
        } else {
            $followers = Follower::where('user_id', $request->user_id)->pluck('follower_id')->toArray();
            $all_data = User::select('id', 'name', 'avatar as image')->whereIn('id', $followers)->paginate($paginationValue);
        }

        foreach ($all_data as $key => $data) {
            $data->image = url('/') . '/' . $data->image;

            $total_follower = Follower::where('user_id', $data->id)->count();
            $data->total_followers = $total_follower;

            if (api_user()) {
                $follow = Follower::where('user_id', $data->id)->where('follower_id', api_user()->id)->first();
                $data->is_following = $follow ? 1 : 0;
            } else {
                $data->is_following = 0;
            }
        }

        return response()->json([
            'status_code' => 200,
            'message' => 'Data retrieve successfully',
            'data' => $all_data,
        ]);

    }
}
