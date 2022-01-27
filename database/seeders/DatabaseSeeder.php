<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // create a main user for login and access
        User::create(['name'=>'kamesh','email'=>'kameshvinayaka@gmail.com','password'=>Hash::make('123456')]);
    }
}
