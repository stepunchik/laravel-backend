<?php

namespace Database\Seeders;

use App\Models\Conversation;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConversationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::pluck('id')->toArray();
        for ($i = 0; $i < 10; $i++) {
            $firstUser = $users[array_rand($users)];
            
            do {
                $secondUser = $users[array_rand($users)];
            } while ($secondUser === $firstUser);

            Conversation::create([
                'name' => fake()->words(2, true),
                'first_user' => $firstUser,
                'second_user' => $secondUser,
            ]);
        }
    }
}
