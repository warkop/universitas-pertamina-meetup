<?php

namespace Database\Seeders;

use App\Models\ResearchGroupComment;
use Illuminate\Database\Seeder;

class ResearchGroupCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ResearchGroupComment::factory(10)->create();
    }
}
