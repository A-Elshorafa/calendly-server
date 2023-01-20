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
            "password" => bcrypt("123456"),
            "email" => "bedo0o_madrid@hotmail.com"
        ]);
    }
}
