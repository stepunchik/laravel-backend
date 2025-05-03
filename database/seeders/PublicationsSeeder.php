<?php

namespace Database\Seeders;

use App\Models\Publication;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PublicationsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Publication::create([
            'title' => fake()->sentence,
            'text' => fake()->paragraph,
            'image' => fake()->imageUrl(),
            'user_id' => User::inRandomOrder()->first()->id
        ]);
    }
}
