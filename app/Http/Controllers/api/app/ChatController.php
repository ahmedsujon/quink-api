<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Chat;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ChatController extends Controller
{
    public function allChats(Request $request)
    {
        try {
            $pagination_value = $request->per_page ? $request->per_page : 10;
            $allChats = Chat::orderBy('id', 'DESC')->paginate($pagination_value);



            if ($allChats->count() > 0) {
                return response()->json([
                    'status_code' => 200,
                    'message' => 'Data retrieve successfully',
                    'data' => $allChats,
                ]);
            } else {
                return response()->json([
                    'status_code' => 404,
                    'message' => 'No Notification Found'
                ]);
            }
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
