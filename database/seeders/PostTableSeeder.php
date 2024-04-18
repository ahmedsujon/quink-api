<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $faker = Faker::create();

            $type = $faker->randomElement(['photo', 'video', 'text']);
            if ($type == 'photo') {
                $content = 'assets/images/post-img.jpg';
                $caption = $faker->sentence(1);
            } else if ($type == 'video') {
                $content = 'assets/videos/reel_' . $faker->randomElement(['a', 'b', 'c']) . '.mp4';
                $caption = $faker->sentence(1);
            } else {
                $content = $faker->paragraph;
                $caption = $faker->sentence(1);
            }

            $post = new Post();
            $post->user_id = rand(1,2);
            $post->caption = $caption;
            $post->content = $content;
            $post->tags = [];
            $post->type = $type;
            $post->save();
        }
    }
}
