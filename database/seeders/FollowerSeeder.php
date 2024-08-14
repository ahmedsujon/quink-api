<?php

namespace Database\Seeders;

use App\Models\Follower;
use Illuminate\Database\Seeder;

class FollowerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $followers = [2, 3, 4, 5, 6, 7, 8, 9, 10];
        foreach ($followers as $key => $user_id) {
            $getData = Follower::where('user_id', 1)->where('follower_id', $user_id)->first();

            if (!$getData) {
                $data = new Follower();
                $data->user_id = 1;
                $data->follower_id = $user_id;
                $data->save();
            }
        }

        $sFollowers = [1, 3, 4, 5, 6, 7, 8, 9, 10];
        foreach ($sFollowers as $key => $s_user_id) {
            $getSData = Follower::where('user_id', 2)->where('follower_id', $s_user_id)->first();
            if (!$getSData) {
                $sData = new Follower();
                $sData->user_id = 2;
                $sData->follower_id = $s_user_id;
                $sData->save();
            }
        }

    }
}
