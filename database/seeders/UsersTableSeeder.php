<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::create([
            "name" => "Shorafa",
            "password" => bcrypt("Masaq12."),
            "email" => "shorafa_masaq@hotmail.com"
        ]);
    }
}
