<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Models\Like;
use App\Models\Post;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 25; $i++) {
            $faker = Faker::create();

            $type = $faker->randomElement(['photo', 'video', 'story']);
            if ($type == 'photo') {
                $content = 'assets/images/post-img.jpg';
                $title = $faker->sentence(1);
            } else if ($type == 'video') {
                $content = 'assets/videos/reel_' . $faker->randomElement(['a', 'b', 'c']) . '.mp4';
                $title = $faker->sentence(1);
            } else {
                $content = 'assets/images/post-img.jpg';
                $title = $faker->sentence(1);
            }

            $post = new Post();
            $post->user_id = $i == 2 ? 1 : rand(1, 2);
            $post->title = $title;
            $post->description = $faker->paragraph(2);
            $post->content = $content;
            $post->hash_tags = $faker->randomElement([["HASH_1", "HASH_2"], ["HASH_1"], ["HASH_3", "HASH_4",], ["HASH_1", "HASH_4"]]);
            $post->tags = $faker->randomElement([[3,4], [3], [4,5], [5]]);
            $post->type = $type;
            $post->save();

            $com_st = rand(0, 1);
            if ($com_st == 1) {
                for ($j = 0; $j < 15; $j++) {
                    $comment = new Comment();
                    $comment->user_id = rand(3, 7);
                    $comment->post_id = $post->id;
                    $comment->comment = $faker->sentence(3);
                    $comment->status = 1;
                    $comment->save();

                    $comment_like_st = rand(0, 1);
                    if ($comment_like_st == 1) {
                        for ($l = 0; $l < rand(0, 7); $l++) {
                            $c_like = new CommentLike();
                            $c_like->user_id = rand(1, 7);
                            $c_like->comment_id = $comment->id;
                            $c_like->save();
                        }
                    }

                    $reply = rand(0, 1);
                    if ($reply == 1) {
                        for ($k = 0; $k < rand(1, 3); $k++) {
                            $reply = new Comment();
                            $reply->parent_id = $comment->id;
                            $reply->user_id = rand(3, 7);
                            $reply->post_id = $post->id;
                            $reply->comment = $faker->sentence(3);
                            $reply->status = 1;
                            $reply->save();
                        }
                    }

                }
            }

            $like_st = rand(0, 1);
            if ($like_st == 1) {
                for ($m = 0; $m < rand(0, 7); $m++) {
                    $like = new Like();
                    $like->user_id = rand(1, 7);
                    $like->post_id = $post->id;
                    $like->save();
                }
            }
        }
    }
}
