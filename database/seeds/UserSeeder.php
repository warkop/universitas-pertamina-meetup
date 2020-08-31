<?php

use App\Model\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $data = [
            [
                'username'  => 'super_admin',
                'password'  => bcrypt('qwerty'),
                'role_id'   => 1,
                'type'      => 1,
                'is_active'      => true,
                'remember_token' => Str::random(10),
            ],
        ];

        foreach ($data as $key) {
            User::updateOrCreate([
                'username' => $key['username']
            ],[
                'username' => $key['username'],
                'role_id'  => $key['role_id'],
                'password' => $key['password'],
                'type'     => $key['type'],
                'role_id'  => $key['role_id'],
                'is_active'  => $key['role_id'],
            ]);
        }
    }
}
