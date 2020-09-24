<?php

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
        factory(ResearchGroupDiscussion::class, 10)->create();
    }
}
