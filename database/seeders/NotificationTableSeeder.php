<?php

namespace Database\Seeders;

use App\Models\Notification;
use Illuminate\Database\Seeder;

class NotificationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $notifications = [
            [
                'for' => 1,
                'user_id' => 4,
                'post_id' => null,
                'comment_id' => null,
                'notification_text' => 'started following you.',
                'type' => 'follow'
            ],
            [
                'for' => 1,
                'user_id' => 4,
                'post_id' => 3,
                'comment_id' => null,
                'notification_text' => 'liked your post.',
                'type' => 'like'
            ],
            [
                'for' => 1,
                'user_id' => 4,
                'post_id' => 3,
                'comment_id' => 419,
                'notification_text' => 'commented on your post.',
                'type' => 'comment'
            ],
            [
                'for' => 1,
                'user_id' => 4,
                'post_id' => 3,
                'comment_id' => null,
                'notification_text' => 'bookmarked your post.',
                'type' => 'bookmark'
            ],
        ];

        foreach ($notifications as $key => $not) {
            $notification = new Notification();
            $notification->notification_for = $not['for'];
            $notification->user_id = $not['user_id'];
            $notification->post_id = $not['post_id'];
            $notification->comment_id = $not['comment_id'];
            $notification->notification_text = $not['notification_text'];
            $notification->type = $not['type'];
            $notification->save();
        }
    }
}
