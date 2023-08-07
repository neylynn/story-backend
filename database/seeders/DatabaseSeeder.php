<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        //     'password' => 'test@example.com',
        //     'email' => 'test@example.com',
        // ]);

        \App\Models\Story::factory(10)->create();

        \App\Models\Story::factory()->create([
            'title' => 'Test title',
            'content' => 'test content',
            'status' => 'test status',
        ]);
    }
}
