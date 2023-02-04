<?php

namespace Database\Seeders;

use App\Models\UserEventStatus;
use Illuminate\Database\Seeder;

class UserEventStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // define all statuses needed for user events
        // separate statuses to enable search with index
        // ex: select all events where status === 1 (pending)
        $statuses = [
            ['name' => 'pending'],
            ['name' => 'expired'],
            ['name' => 'up coming'],
        ];

        UserEventStatus::insert($statuses);
    }
}
