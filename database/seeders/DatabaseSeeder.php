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

         \App\Models\Type::create([
             'name' => 'servers',
         ]);
        \App\Models\Type::create([
            'name' => 'product',
        ]);
        \App\Models\Type::create([
            'name' => 'location',
        ]);
        \App\Models\Type::create([
            'name' => 'course',
        ]);
        \App\Models\Type::create([
            'name' => 'sup',
        ]);
    }
}
