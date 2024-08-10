<?php

namespace App\Http\Controllers\api\app;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function startChat(Request $request)
    {
        //Validation
        $rules = [
            'sender_id' => 'required',
            'receiver_id' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $sender = $request->sender_id;
            $receiver = $request->receiver_id;

            $getChat = Chat::where(function ($qa) use ($sender, $receiver) {
                $qa->where('sender', $sender)
                    ->where('receiver', $receiver);
            })->orWhere(function ($qb) use ($sender, $receiver) {
                $qb->where('sender', $receiver)
                    ->where('receiver', $sender);
            })->first();

            if ($getChat) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Chat started successfully',
                    'data' => [
                        'chat_id' => $getChat->id,
                    ],
                ]);
            } else {
                $chat = new Chat();
                $chat->sender = $request->sender_id;
                $chat->receiver = $request->receiver_id;
                $chat->status = 1;
                $chat->save();

                return response()->json([
                    'status_code' => 200,
                    'message' => 'Chat started successfully',
                    'data' => [
                        'chat_id' => $chat->id,
                    ],
                ]);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }

    }

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
                ->where(function ($q) use ($authUserId) {
                    $q->where('chats.sender', $authUserId)
                        ->orWhere('chats.receiver', $authUserId);
                })
                ->where(function ($query) use ($searchValue, $authUserId) {
                    $query->where(function ($subQuery) use ($searchValue, $authUserId) {
                        $subQuery->where('sender_user.name', 'like', "%{$searchValue}%")
                            ->where('chats.receiver', $authUserId);
                    })->orWhere(function ($subQuery) use ($searchValue, $authUserId) {
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

            if ($chats->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $chats,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No Chats Found',
                ]);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function chatMessages(Request $request)
    {
        try {
            $pagination_value = $request->per_page ? $request->per_page : 20;
            $messages = Message::where('chat_id', $request->chat_id)->orderBy('created_at', 'asc')->paginate($pagination_value);

            $chat = Chat::find($request->chat_id);
            if ($chat->sender == api_user()->id) {
                $user = User::find($chat->receiver);
            } else {
                $user = User::find($chat->sender);
            }

            $groupedMessages = $messages->groupBy(function ($message) {
                return Carbon::parse($message->created_at)->format('Y-m-d'); // Group by date
            });

            // Format the response
            $response = [
                'user_info' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'image' => url('/') . '/' . ($user->avatar ? $user->avatar : 'assets/images/avatar.png'),
                    'is_online' => 0,
                    'is_verified' => $user->email_verified_at ? 1 : 0,
                ],
                'current_page' => $messages->currentPage(),
                'data' => [],
                'first_page_url' => $messages->url(1),
                'from' => $messages->firstItem(),
                'last_page' => $messages->lastPage(),
                'last_page_url' => $messages->url($messages->lastPage()),
                'links' => [],
                'next_page_url' => $messages->nextPageUrl(),
                'path' => $messages->path(),
                'per_page' => $messages->perPage(),
                'prev_page_url' => $messages->previousPageUrl(),
                'to' => $messages->lastItem(),
                'total' => $messages->total(),
            ];

            foreach ($groupedMessages as $date => $messagesOnDate) {

                foreach ($messagesOnDate as $key => $dm) {
                    $dm->file = $dm->file ? url('/') . '/' . $dm->file : '';
                    $dm->time = Carbon::parse($dm->created_at)->format('H:i A');
                    // if ($dm->sender == api_user()->id) {
                    //     $dm->sent_from = 'me';
                    // } else {
                    //     $dm->sent_from = 'user';
                    // }
                }

                $response['data'][] = [
                    'date' => $date,
                    'messages' => $messagesOnDate,
                ];
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Data retrieve successfully',
                'data' => $response,
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }

    public function sendMessage(Request $request)
    {

        //Validation
        $rules = [
            'chat_id' => 'required',
            'sender' => 'required',
            'receiver' => 'required',
            'message' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        try {
            $message = new Message();
            $message->chat_id = $request->chat_id;
            $message->sender = $request->sender;
            $message->receiver = $request->receiver;
            $message->message = $request->message;

            if ($request->file) {
                $file = uploadFile($request->file, 'messages');
                $message->file = $file;
                $message->file_type = $request->file_type;
            }

            if ($message->save()) {
                $content = [
                    "id" => $message->id,
                    "chat_id" => $message->chat_id,
                    "sender" => $message->sender,
                    "receiver" => $message->receiver,
                    "message" => $message->message,
                    "file" => $message->file ? url('/') . '/' . $message->file : '',
                    "file_type" => $message->file_type,
                    "time" => Carbon::parse($message->created_at)->format('H:i A'),
                    "status" => $message->status,
                    "created_at" => $message->created_at,
                    "updated_at" => $message->updated_at,
                ];

                $socket_server = env('SOCKET_SERVER');

                $response = Http::post(''.$socket_server.'/send_message', [
                    'content' => $content,
                ]);
            }

            return response()->json([
                'status_code' => 200,
                'message' => 'Message Sent Successfully',
            ]);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }

    }
}
