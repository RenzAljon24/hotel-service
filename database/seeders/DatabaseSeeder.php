<?php

namespace Database\Seeders;

use App\Models\Hotel;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Room;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();


        // Create multiple rooms
        Room::factory()->count(100)->create();
  
    }
}
