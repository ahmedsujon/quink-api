<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserMeasurement;
use App\Models\WaterSetting;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $emails = ["user@example.com", "user01@example.com", "user02@example.com", "user03@example.com", "user04@example.com", "user05@example.com", "user06@example.com", "user07@example.com", "user08@example.com", "user09@example.com"];

        foreach ($emails as $key => $email) {
            $getUser = User::where('email', $email)->first();

            $faker = Faker::create();
            $name = $faker->unique(true)->words($nb = 2, $asText = true);

            if (!$getUser) {
                $user = new User();
                $user->name = ucwords($name);
                $user->username = 'user' . rand(10, 99);
                $user->email = $email;
                $user->email_verified_at = now();
                $user->password = Hash::make('12345678');
                $user->avatar = 'assets/images/avatar.png';
                $user->gender = $faker->randomElement(['Male', 'Female']);
                $user->save();
            }
        }
    }

}
