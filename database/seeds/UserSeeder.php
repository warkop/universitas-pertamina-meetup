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
                'email'             => 'admin@energeek.co.id',
                'password'          => bcrypt('qwerty'),
                'role_id'           => 1,
                'type'              => 2,
                'status'            => 1,
                'email_verified_at' => now(),
                'confirm_at'        => now(),
                'remember_token'    => Str::random(10),
            ],
        ];

        foreach ($data as $key) {
            User::updateOrCreate([
                'email'    => $key['email']
            ],[
                'email'                 => $key['email'],
                'role_id'               => $key['role_id'],
                'password'              => $key['password'],
                'type'                  => $key['type'],
                'status'                => $key['status'],
                'email_verified_at'     => $key['email_verified_at'],
                'confirm_at'            => $key['confirm_at'],
                'remember_token'        => $key['remember_token'],
            ]);
        }
    }
}
