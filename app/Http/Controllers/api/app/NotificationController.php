<?php

namespace App\Http\Controllers\api\app;

use Exception;
use App\Models\Notification;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationController extends Controller
{
    public function notifications(Request $request)
    {
        try {
            $pagination_value = $request->per_page ? $request->per_page : 10;
            $notifications = Notification::where('notification_for', api_user()->id)->orderBy('id', 'DESC')->get(); //->paginate($pagination_value)

            foreach ($notifications as $key => $notification) {

            }

            return response()->json($notifications);
        } catch (Exception $ex) {
            return response($ex->getMessage());
        }
    }
}
