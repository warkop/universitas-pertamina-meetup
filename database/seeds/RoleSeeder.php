<?php

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleData = [
            [
                'id'    => 1,
                'name'  => 'super_user',
            ],
            [
                'id'    => 2,
                'name'  => 'regular_user',
            ],
            [
                'id'    => 3,
                'name'  => 'institution',
            ],
        ];

        foreach ($roleData as $key) {
            Role::updateOrCreate([
                'id' => $key['id']
            ],[
                'id'    => $key['id'],
                'name'  => $key['name']
            ]);
        }
    }
}
