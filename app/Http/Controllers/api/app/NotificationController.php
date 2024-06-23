<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Comment;
use Illuminate\Support\Str;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post;

class NotificationController extends Controller
{
    public function notifications(Request $request)
    {
        try {
            // $pagination_value = $request->per_page ? $request->per_page : 10;
            $notifications = Notification::where('notification_for', api_user()->id)->orderBy('id', 'DESC')->get(); //->paginate($pagination_value)

            $notification_array = [];
            foreach ($notifications as $key => $notification) {
                $user = getUserByID($notification->user_id);
                $comment = Comment::find($notification->comment_id);
                $comment_text = $comment ? Str::limit($comment->comment, 35, '...') : '';
                $post_info = [];
                if ($notification->post_id) {
                    $post = Post::find($notification->post_id);
                    $post_info = [
                        'id' => $post->id,
                        'type' => $post->type,
                        'content' => $post->content ? url('/') . '/' . $post->content : null
                    ];
                }
                $notification_array[] = [
                    'notification' => $user->name . ' ' . $notification->notification_text,
                    'time' => short_time($user->created_at),
                    'user_info' => [
                        'user_id' => $user->id,
                        'name' => $user->name,
                        'image' => $user->avatar ? url('/') . '/' . $user->avatar : url('/') . '/assets/images/avatar.png',
                    ],
                    'post_info' => $post_info,
                    'comment_id' => $notification->comment_id,
                    'comment' => $comment_text,
                    'type' => $notification->type,
                ];
            }

            if ($notifications->count() > 0) {
                return response()->json($notification_array);
            } else {
                return response()->json(['result' => false, 'message' => 'No Notification Found']);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
