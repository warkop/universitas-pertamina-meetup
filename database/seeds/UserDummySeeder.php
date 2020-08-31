<?php

use App\Model\User;
use Illuminate\Database\Seeder;

class UserDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(User::class, 5)->create();
    }
}
