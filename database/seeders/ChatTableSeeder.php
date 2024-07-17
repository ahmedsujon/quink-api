<?php

namespace Database\Seeders;

use App\Models\Chat;
use App\Models\Message;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class ChatTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 2; $i <= 6; $i++) {
            $faker = Faker::create();

            $chat = new Chat();
            $chat->sender = 1;
            $chat->receiver = $i;
            $chat->last_msg = str_replace('.', '', $faker->sentence(1));
            $chat->status = 1;
            $chat->save();

            for ($j = 0; $j < 14; $j++) {
                $rand = rand(0, 1);
                $rand_date = rand(1, 3);

                $message = new Message();
                $message->chat_id = $chat->id;
                $message->sender = $rand == 0 ? $i : 1;
                $message->receiver = $rand == 1 ? $i : 1;
                $message->message = $faker->sentence(5);
                $message->file = 'assets/images/placeholder.jpg';
                $message->file_type = 'image';
                $message->status = 1;
                $message->created_at = now()->subDays($rand_date);
                $message->save();
            }

        }
    }
}
