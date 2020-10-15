<?php

namespace Database\Seeders;

use App\Models\ResearchGroupDiscussion;
use Illuminate\Database\Seeder;

class ResearchGroupDiscussionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ResearchGroupDiscussion::factory(10)->create();
    }
}
