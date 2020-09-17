<?php

use App\Models\User;
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
                'username'          => 'super_admin',
                'password'          => bcrypt('qwerty'),
                'role_id'           => 1,
                'type'              => 2,
                'status'            => 1,
                'email_verified_at' => now(),
                'confirm_at'        => now(),
                'confirm_by'        => 0,
                'remember_token'    => Str::random(10),
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
                'status'   => $key['status'],
            ]);
        }
    }
}
