<?php

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
        factory(ResearchGroupComment::class, 20)->create();
    }
}
