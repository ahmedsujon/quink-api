<?php

namespace App\Http\Controllers\api\app;

use Exception;
use Carbon\Carbon;
use App\Models\Chat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function allChats(Request $request)
    {
        try {
            $pagination_value = $request->per_page ? $request->per_page : 10;

            $searchValue = $request->search_value;
            $authUserId = api_user()->id;

            $chats = DB::table('chats')
            ->join('users as sender_user', 'chats.sender', 'sender_user.id')
            ->join('users as receiver_user', 'chats.receiver', 'receiver_user.id')
            ->select(
                'chats.id',
                'chats.sender',
                'chats.receiver',
                'chats.last_msg',
                'chats.updated_at as time',
                'chats.status as is_read'
            )
            ->where(function($q) use ($authUserId) {
                $q->where('chats.sender', $authUserId)
                    ->orWhere('chats.receiver', $authUserId);
            })
            ->where(function($query) use ($searchValue, $authUserId) {
                $query->where(function($subQuery) use ($searchValue, $authUserId) {
                    $subQuery->where('sender_user.name', 'like', "%{$searchValue}%")
                             ->where('chats.receiver', $authUserId);
                })->orWhere(function($subQuery) use ($searchValue, $authUserId) {
                    $subQuery->where('receiver_user.name', 'like', "%{$searchValue}%")
                             ->where('chats.sender', $authUserId);
                });
            });

            if ($request->filter_value == 'unread') {
                $chats = $chats->where('chats.status', 0);
            }

            $chats = $chats->orderBy('chats.updated_at', 'DESC')->paginate($pagination_value);

            foreach ($chats as $key => $chat) {
                if ($chat->sender == api_user()->id) {
                    $user = User::find($chat->receiver);
                } else {
                    $user = User::find($chat->sender);
                }

                $chat->time = short_time_chat($chat->time);
                $chat->user_info = [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image' => url('/') . '/' . ($user->avatar ? $user->avatar : 'assets/images/avatar.png'),
                    'is_online' => 0,
                    'is_verified' => $user->email_verified_at ? 1 : 0,
                ];
            }

            // if ($chats->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $chats,
                ]);
            // } else {
            //     return response()->json([
            //         'status_code' => 404,
            //         'message' => 'No Chats Found'
            //     ]);
            // }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
