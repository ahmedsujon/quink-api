<?php

namespace Database\Seeders;

use App\Models\Follower;
use App\Models\Post;
use App\Models\PostTag;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PostTagsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $myFollowers = Follower::where('user_id', 1)->pluck('follower_id')->toArray();

        $faker = Faker::create();
        $rand = rand(2,5);

        $myPosts = Post::where('user_id', 1)->take('10')->pluck('id')->toArray();
        foreach ($myPosts as $mPostID) {
            $postTag = new PostTag();
            $postTag->post_id = $mPostID;
            $postTag->mentioned_users = $faker->randomElements($myFollowers, $rand);
            $postTag->save();
        }

        $userFollowers = Follower::where('user_id', 2)->pluck('follower_id')->toArray();

        $userPosts = Post::where('user_id', 2)->take('5')->pluck('id')->toArray();
        foreach ($userPosts as $uPostID) {
            $uPostTag = new PostTag();
            $uPostTag->post_id = $uPostID;
            $uPostTag->mentioned_users = $userFollowers;
            $uPostTag->save();
        }
    }
}
