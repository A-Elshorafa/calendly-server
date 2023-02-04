<?php

namespace Database\Seeders;

use App\Models\ThirdParty;
use Illuminate\Database\Seeder;

class ThirdPartiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ThirdParty::create([
            'name' => 'zoom'
        ]);
    }
}
